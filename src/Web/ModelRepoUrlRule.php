<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;


use Kukusa;
use Kukusa\Base\ModelRepo;
use yii\base\InvalidConfigException;
use yii\helpers\Inflector;
use yii\web\CompositeUrlRule;
use yii\web\UrlRule as WebUrlRule;
use yii\web\UrlRuleInterface;

class ModelRepoUrlRule extends CompositeUrlRule
{
    public $tokens = [
        '{id}' => '<id:\w[a-zA-Z0-9._+-]*>',
        '{module}' => '<' . ModelRepo::MODULE_KEY . ':[a-zA-Z0-9-]*>',
        '{model}' => '<' . ModelRepo::MODEL_KEY . ':[a-zA-Z0-9-]*>',
        '{action}' => '<action:[a-zA-Z0-9]*>',
        '{customAction}' => '<' . ModelRepo::CUSTOM_ACTION_KEY . '>',
        '{args}' => '<args:[a-zA-Z0-9\\/-\\;"]*>',
    ];

    public $patterns = [
        'GET {module}/{model}' => 'index',
        'GET {module}/{model}/index' => 'index',
        'GET,POST {module}/{model}/create' => 'create',
        'GET,POST {module}/{model}/update/{id}' => 'update',
        'GET {module}/{model}/view/{id}' => 'view',
        'POST {module}/{model}/delete/{id}' => 'delete',
        '{module}/{model}/__rest/<restAction>/{id}' => '__rest',
        '{module}/{model}/__rest/<restAction>' => '__rest',
//        '{module}/{model}/{customAction}' => '<'.ModelRepo::CUSTOM_ACTION_KEY.'>',
    ];
    /**
     * @var string the suffix that will be assigned to [[\yii\web\UrlRule::suffix]] for every generated rule.
     */
    public $suffix;

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
     * @var array the default configuration for creating each URL rule contained by this rule.
     */
    public $ruleConfig = [
        'class' => 'yii\web\UrlRule',
    ];


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (empty($this->controller)) {
            throw new InvalidConfigException('"controller" must be set.');
        }
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
        foreach ($patterns as $pattern => $action) {
            if (!isset($except[$action]) && (empty($only) || isset($only[$action]))) {
                $rules[] = $this->createRule($pattern, $this->controller . '/' . $action);
            }
        }
        return $rules;
    }

    /**
     * Creates a URL rule using the given pattern and action.
     * @param string $pattern
     * @param string $action
     * @return UrlRuleInterface
     * @throws InvalidConfigException
     */
    protected function createRule($pattern, $action)
    {
        $verbs = 'GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS';
        if (preg_match("/^((?:($verbs),)*($verbs))(?:\\s+(.*))?$/", $pattern, $matches)) {
            $verbs = explode(',', $matches[1]);
            $pattern = isset($matches[4]) ? $matches[4] : '';
        } else {
            $verbs = [];
        }
        $pattern = Kukusa::$app->modelRepo->prefixUri . '/' . $pattern;

        $config = $this->ruleConfig;
        $config['verb'] = $verbs;
        $config['pattern'] = rtrim(strtr($pattern, $this->tokens), '/');
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
        if (strpos($pathInfo, Kukusa::$app->modelRepo->prefixUri) !== false) {
            foreach ($this->rules as $rule) {
                /* @var $rule WebUrlRule */
                $result = $rule->parseRequest($manager, $request);
                if (YII_DEBUG) {
                    Kukusa::debug([
                        'rule' => method_exists($rule, '__toString') ? $rule->__toString() : get_class($rule),
                        'match' => $result !== false,
                        'parent' => self::class,
                    ], __METHOD__);
                }
                if ($result !== false) {
                    return $result;
                }
            }
        }


        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function createUrl($manager, $route, $params)
    {
//        echo 'asa';exit;
        $this->createStatus = WebUrlRule::CREATE_STATUS_SUCCESS;
        if (strpos($route, $this->controller) !== false) {
            /* @var $rules UrlRuleInterface[] */
            $rules = $this->rules;
            $url = $this->iterateRules($rules, $manager, $route, $params);
            if ($url !== false) {
                return $url;
            }
        } else {
            $this->createStatus |= WebUrlRule::CREATE_STATUS_ROUTE_MISMATCH;
        }

        if ($this->createStatus === WebUrlRule::CREATE_STATUS_SUCCESS) {
            // create status was not changed - there is no rules configured
            $this->createStatus = WebUrlRule::CREATE_STATUS_PARSING_ONLY;
        }

        return false;
    }
}