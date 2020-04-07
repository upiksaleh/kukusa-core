<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;


use Kukusa;
use Kukusa\Helpers\Url;
use yii\base\Component;
use Kukusa\Helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Class DataModel
 * @package Kukusa\Base
 * @property array $models
 * @property BaseModelController $currentModel
 * @property string $currentModuleName
 * @property string $currentModelName
 * @property string|BaseModelController $currentModelClassName
 * @property string $prefixUri
 * @property string $prefixUriApi
 */
class ModelRepo extends Component
{
    const MODULE_KEY = '_modelRepoModule';
    const MODEL_KEY = '_modelRepoModel';
    const CUSTOM_ACTION_KEY = '_modelRepoCustomAction';

    const FORM_TYPE_CREATE = Kukusa\Base\Model::SCENARIO_CREATE;
    const FORM_TYPE_UPDATE = Kukusa\Base\Model::SCENARIO_UPDATE;

    private $_instance = [];
    private $_models = [];
    private $_prefixUriApi = 'api';
    private $_prefixUri = 'v1';
    /** @var string */
    private $_currentModuleName;
    /** @var string */
    private $_currentModelName;
    /** @var BaseModelController */
    private $_currentModel;
    /** @var string */
    private $_currentModelClassName;

    public function exist($module, $key)
    {
        return ArrayHelper::getValue($this->_models, $module . '.' . $key, false);
    }

    /**
     * @param $module
     * @param $model
     * @return string|false
     */
    public function getModelClassName($module, $model)
    {
        if (!$this->exist($module, $model))
            return false;

        $class = ArrayHelper::getValue($this->_models, $module . '.' . $model);
        if (is_string($class)) return $class;
        elseif (is_array($class) && isset($class['class'])) return $class['class'];
        elseif ($class instanceof BaseModelController) return get_class($class);

        return false;
    }

    /**
     * @param $module
     * @param $model
     * @return object|BaseModelController|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getModelObj($module, $model)
    {
        if (isset($this->_instance[$module . '/' . $model]))
            return $this->_instance[$module . '/' . $model];
        if (!$this->exist($module, $model))
            return null;

        $class = ArrayHelper::getValue($this->_models, $module . '.' . $model);
        /** @var BaseModelController $class */
        $class = \Kukusa::createObject($class);
        $class::init_model_repo($module, $model);
        return $this->_instance[$module . '/' . $model] = $class;
    }

    /**
     * @return array
     */
    public function getModels()
    {
        return $this->_models;
    }

    /**
     * @param array $models
     */
    public function setModels(array $models)
    {
        $this->_models = $models;
    }

    public function add($module, $key, $class)
    {
        ArrayHelper::setValue($this->_models, $module . '.' . $key, $class);
    }

    /**
     * @param string $module
     * @param array $models
     */
    public function addBatchModule($module, $models)
    {
        foreach ($models as $item => $value) {
            ArrayHelper::setValue($this->_models, $module . '.' . $item, $value);
        }
    }

    /**
     * @param string $module
     * @param string $model
     */
    public function setCurrent($module, $model)
    {
        $this->_currentModuleName = $module;
        $this->_currentModelName = $model;
        $this->_currentModel = $this->getModelObj($module, $model);
        $this->_currentModelClassName = $this->getModelClassName($module, $model);
    }

    public function setCurrentFromQueryParams()
    {
        if ($this->currentModuleName && $this->currentModelName) {
            $this->setCurrent($this->currentModuleName, $this->currentModelName);
        } else {
            throw new InvalidConfigException(Kukusa::t('kukusa', 'Tidak ditemukan query params.'));
        }

    }

    public function prefix_url($path)
    {
        return $this->prefixUri . '/' . $path;
    }

    public function _to_url($path, $params = [], $returnUrl = false)
    {
        $params[0] = $this->prefix_url($path);
        if ($returnUrl)
            return Url::to($params);
        return $params;
    }

    public static function to_url($path, $params = [], $returnUrl = false)
    {
        return Kukusa::$app->modelRepo->_to_url($path, $params);
    }

    // --------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getCurrentModuleName()
    {
        return Kukusa::$app->request->getQueryParam(static::MODULE_KEY, $this->_currentModuleName);
    }

    /**
     * @return mixed
     */
    public function getCurrentModelName()
    {
        return Kukusa::$app->request->getQueryParam(static::MODEL_KEY, $this->_currentModelName);
    }

    /**
     * @return BaseModelController
     */
    public function getCurrentModel()
    {
        return $this->_currentModel;
    }

    /**
     * @return string
     */
    public function getCurrentModelClassName()
    {
        return $this->_currentModelClassName;
    }

    /**
     * @return string
     */
    public function getPrefixUri()
    {
        return $this->_prefixUri;
    }

    /**
     * @param string $prefixUri
     */
    public function setPrefixUri($prefixUri)
    {
        $this->_prefixUri = $prefixUri;
    }

    /**
     * @return string
     */
    public function getPrefixUriApi()
    {
        return $this->_prefixUriApi;
    }

    /**
     * @param string $prefixUriApi
     */
    public function setPrefixUriApi(string $prefixUriApi)
    {
        $this->_prefixUriApi = $prefixUriApi;
    }

}