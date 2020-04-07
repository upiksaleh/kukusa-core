<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;

use Kukusa;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\web\CompositeUrlRule;
use yii\web\UrlRule as WebUrlRule;
use yii\web\UrlRuleInterface;

class RestUrlRule extends CompositeUrlRule
{
    /**
     * @var string the common prefix string shared by all patterns.
     */
    public $prefix = 'api';
    /**
     * @var string the suffix that will be assigned to [[\yii\web\UrlRule::suffix]] for every generated rule.
     */
    public $suffix;
    /**
     * @var string|array the controller ID (e.g. `user`, `post-comment`) that the rules in this composite rule
     * are dealing with. It should be prefixed with the module ID if the controller is within a module (e.g. `admin/user`).
     *
     * By default, the controller ID will be pluralized automatically when it is put in the patterns of the
     * generated rules. If you want to explicitly specify how the controller ID should appear in the patterns,
     * you may use an array with the array key being as the controller ID in the pattern, and the array value
     * the actual controller ID. For example, `['u' => 'user']`.
     *
     * You may also pass multiple controller IDs as an array. If this is the case, this composite rule will
     * generate applicable URL rules for EVERY specified controller. For example, `['user', 'post']`.
     */
    public $controller;
    /**
     * @var array list of acceptable actions. If not empty, only the actions within this array
     * will have the corresponding URL rules created.
     * @see patterns
     */
    public $only = [];
    /**
     * @var array list of actions that should be excluded. Any action found in this array
     * will NOT have its URL rules created.
     * @see patterns
     */
    public $except = [];
    /**
     * @var array patterns for supporting extra actions in addition to those listed in [[patterns]].
     * The keys are the patterns and the values are the corresponding action IDs.
     * These extra patterns will take precedence over [[patterns]].
     */
    public $extraPatterns = [];
    /**
     * @var array list of tokens that should be replaced for each pattern. The keys are the token names,
     * and the values are the corresponding replacements.
     * @see patterns
     */
    public $tokens = [
        '{id}' => '<id:\\d[\\d,]*>',
        '{model}' => '<model:[a-zA-Z0-9/-]*>',
        '{action}' => '<action:[a-zA-Z0-9]*>',
        '{args}' => '<args:[a-zA-Z0-9\\/-\\;"]*>',
    ];
    /**
     * @var array list of possible patterns and the corresponding actions for creating the URL rules.
     * The keys are the patterns and the values are the corresponding actions.
     * The format of patterns is `Verbs Pattern`, where `Verbs` stands for a list of HTTP verbs separated
     * by comma (without space). If `Verbs` is not specified, it means all verbs are allowed.
     * `Pattern` is optional. It will be prefixed with [[prefix]]/[[controller]]/,
     * and tokens in it will be replaced by [[tokens]].
     */
    public $patterns = [
        'GET,HEAD,OPTIONS {model}/model-config' => 'model-config',
        '{model}/action/{action}/{args}' => 'custom-action',
        'PUT,PATCH {model}/{id}' => 'update',
        'DELETE {model}/{id}' => 'delete',
        'GET,HEAD {model}/{id}' => 'view',
        'POST {model}' => 'create',
        'GET,HEAD {model}' => 'index',
        '{model}/{id}' => 'options',
        '{model}' => 'options',
    ];
    /**
     * @var array the default configuration for creating each URL rule contained by this rule.
     */
    public $ruleConfig = [
        'class' => 'yii\web\UrlRule',
    ];
    /**
     * @var bool whether to automatically pluralize the URL names for controllers.
     * If true, a controller ID will appear in plural form in URLs. For example, `user` controller
     * will appear as `users` in URLs.
     * @see controller
     */
    public $pluralize = true;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (empty($this->controller)) {
            throw new InvalidConfigException('"controller" must be set.');
        }

        $controllers = [];
        foreach ((array)$this->controller as $urlName => $controller) {
            if (is_int($urlName)) {
                $urlName = $this->pluralize ? Inflector::pluralize($controller) : $controller;
            }
            $controllers[$urlName] = $controller;
        }
        $this->controller = $controllers;

        $this->prefix = trim($this->prefix, '/');

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    protected function createRules()
    {
        $only = array_flip($this->only);
        $except = array_flip($this->except);
        $patterns = $this->extraPatterns + $this->patterns;
        $rules = [];
        foreach ($this->controller as $urlName => $controller) {
            $prefix = trim($this->prefix . '/' . $urlName, '/');
            foreach ($patterns as $pattern => $action) {
                if (!isset($except[$action]) && (empty($only) || isset($only[$action]))) {
                    $rules[$urlName][] = $this->createRule($pattern, $prefix, $controller . '/' . $action);
                }
            }
        }

        return $rules;
    }

    /**
     * Creates a URL rule using the given pattern and action.
     * @param string $pattern
     * @param string $prefix
     * @param string $action
     * @return UrlRuleInterface
     */
    protected function createRule($pattern, $prefix, $action)
    {
        $verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
        if (preg_match("/^((?:($verbs),)*($verbs))(?:\\s+(.*))?$/", $pattern, $matches)) {
            $verbs = explode(',', $matches[1]);
            $pattern = isset($matches[4]) ? $matches[4] : '';
        } else {
            $verbs = [];
        }

        $config = $this->ruleConfig;
        $config['verb'] = $verbs;
        $config['pattern'] = rtrim($prefix . '/' . strtr($pattern, $this->tokens), '/');
        $config['route'] = $action;
        if (!empty($verbs) && !in_array('GET', $verbs)) {
            $config['mode'] = WebUrlRule::PARSING_ONLY;
        }
        $config['suffix'] = $this->suffix;

        return Kukusa::createObject($config);
    }

    /**
     * {@inheritDoc}
     */
    public function parseRequest($manager, $request)
    {
        $pathInfo = $request->getPathInfo();
        foreach ($this->rules as $urlName => $rules) {
            if (strpos($pathInfo, $urlName) !== false) {
                foreach ($rules as $rule) {
                    /* @var $rule WebUrlRule */
                    $result = $rule->parseRequest($manager, $request);
                    if (YII_DEBUG) {
                        Kukusa::debug([
                            'rule' => method_exists($rule, '__toString') ? $rule->__toString() : get_class($rule),
                            'match' => $result !== false,
                            'parent' => self::className(),
                        ], __METHOD__);
                    }
                    if ($result !== false) {
                        return $result;
                    }
                }
            }
        }

        return false;
    }

    protected function onSuccessUrl($rules, $manager, $route, $params)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function createUrl($manager, $route, $params)
    {
        $this->createStatus = WebUrlRule::CREATE_STATUS_SUCCESS;
        foreach ($this->controller as $urlName => $controller) {
            if (strpos($route, $controller) !== false) {
                /* @var $rules UrlRuleInterface[] */
                $rules = $this->rules[$urlName];
                $url = $this->iterateRules($rules, $manager, $route, $params);
                if ($url !== false) {
                    $this->onSuccessUrl($rules, $manager, $route, $params);
                    return $url;
                }
            } else {
                $this->createStatus |= WebUrlRule::CREATE_STATUS_ROUTE_MISMATCH;
            }
        }

        if ($this->createStatus === WebUrlRule::CREATE_STATUS_SUCCESS) {
            // create status was not changed - there is no rules configured
            $this->createStatus = WebUrlRule::CREATE_STATUS_PARSING_ONLY;
        }

        return false;
    }
}