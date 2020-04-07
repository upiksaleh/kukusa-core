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
use Kukusa\Rbac\RbacHelper;
use yii\web\NotFoundHttpException;

/**
 * Trait BaseModelRepoTrait
 * @package Kukusa\Base
 * @property ModelOptions $modelOptions
 * @property FilterModel $filterModel
 * @property Kukusa\Form\Field[] $normalizeFormFields
 */
trait BaseModelRepoControllerTrait
{

    public static $ACTION_INDEX = 'index';
    public static $ACTION_CREATE = 'create';
    public static $ACTION_UPDATE = 'update';
    public static $ACTION_DELETE = 'delete';
    public static $ACTION_IMPORT = 'import';
    public static $ACTION_EXPORT = 'export';
    /**
     * @var ModelOptions
     */
    private $_modelOptions;
    private $_filterModel;


    private function init_base()
    {
        $this->init_filterModel();
    }

    public function init_filterModel()
    {
        if (!empty($this->modelOptions->filterRules)) {
            $filterModel = FilterModel::newFromRules($this->modelOptions->filterRules);
            $filterModel->setFormName(static::searchClassName());
            $this->_filterModel = $filterModel;
        }
    }

    public static function modelID()
    {
        $classId = explode('\\', static::class);
        $moduleId = '';
        if (Kukusa::$app->controller && (Kukusa::$app->controller->module->id !== Kukusa::$app->id))
            $moduleId = Kukusa::$app->controller->module->id . '-';
        $classId = strtolower(str_replace('Model', '', array_pop($classId)));
        return $moduleId . $classId;
    }

    /**
     * @param \Kukusa\Web\Controller $c
     * @return void
     */
    public static function init_controller_web($c)
    {
    }

    /**
     * @param \Kukusa\Rest\ActiveController $c
     * @return void
     */
    public static function init_controller_rest($c)
    {
    }


    // --------------------------------------------------------------------

    /**
     * jika array maka akan di merge
     * @param array|ModelOptions $options
     */
    public function setModelOptions($options)
    {
        $_options = static::_options();
        if (!$_options) $_options = new ModelOptions();

        if (is_array($options)) {
            foreach ($options as $key => $value) {
                if ($_options->canSetProperty($key))
                    $_options->{$key} = $value;
            }
        } elseif ($options instanceof ModelOptions)
            $_options = $options;
        $this->_modelOptions = $_options;
    }

    /**
     * @return ModelOptions
     */
    public function getModelOptions()
    {
        if (!$this->_modelOptions) $this->setModelOptions([]);
        return $this->_modelOptions;
    }
    // --------------------------------------------------------------------

    /**
     * form field
     * ```php
     * return [
     *      new Field(['name'=>'username']),
     *      new Field(['name'=>'group']),
     *      ...
     * ]
     * @return array
     */
    abstract protected function _form_fields();

    private $_formFields;

    /**
     * get Form Field
     */
    public function form_fields()
    {
        if ($this->_formFields) return $this->_formFields;
        $this->_formFields = $this->_form_fields();
        if (!$this->_formFields)
            $this->_formFields = [];
        return $this->_formFields;
    }

    // --------------------------------------------------------------------

    /**
     * akan dimerge pada actions() controller
     * @return array
     */
    public static function actions()
    {
        return [];
    }

    /**
     * handle find model
     * @param string|array $params
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function findModelOne($params)
    {
        $model = null;
        if ($this instanceof BaseModelController)
            $model = static::findOne($params);
        elseif ($this instanceof BaseModelControllerModel)
            $model = $this->onFindModelOne($params);
        if (!$model)
            throw new NotFoundHttpException();
        return $model;

    }

    public function init_forms($type)
    {
        if ($this instanceof BaseModelController) {
            if ($this === ModelRepo::FORM_TYPE_UPDATE) {

            } else {
                $this->loadDefaultValues();
            }
        }

    }

    /**
     * @return FilterModel|null
     */
    public function getFilterModel()
    {
        return $this->_filterModel;
    }

    /**
     * handle saat filter post di terima
     * @param \yii\db\QueryInterface|\yii\db\ActiveQuery $query
     * @param FilterModel|static $filterModel
     * @return void
     */
    abstract protected function onSearch(&$query, $filterModel);

    /**
     * @param \yii\data\ActiveDataProvider $dataProvider
     */
    protected function onDataProvider(&$dataProvider)
    {
    }

    /**
     * @param \yii\data\ActiveDataProvider $dataProvider
     */
    public function initDataProvider(&$dataProvider)
    {
        $this->onDataProvider($dataProvider);
        if (!$dataProvider->query) {
            $dataProvider->query = static::find();
        }
        $this->prosesFiltering($dataProvider);

    }

    protected function prosesFiltering(&$dataProvider)
    {
        $requestParams = Kukusa::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Kukusa::$app->getRequest()->getQueryParams();
        }
        if ($this->_filterModel && $this->_filterModel->load($requestParams) && $this->_filterModel->validate()) {
            $this->onSearch($dataProvider->query, $this->_filterModel);
        }
    }
    // --------------------------------------------------------------------

    /**
     * get normalize form fields
     * @return Kukusa\Form\Field[]
     */
    public function getNormalizeFormFields()
    {
        $fields = [];
        foreach ($this->form_fields() as $field) {
            $fields[$field->name] = $field;
        }
        return $fields;
    }

    /**
     * mengatur fields jika ingin menggunakan row
     * @return array
     */
    public function form_fields_row()
    {
        return [];
    }

    public function clientConfigFormat()
    {
        return [
            'fields' => $this->form_fields(),
            'rules' => $this->getRuleConfig()
        ];
    }

    public function getClientConfig()
    {
        $this->addScenario(self::SCENARIO_CREATE);
        $this->addScenario(self::SCENARIO_UPDATE);
        $this->scenario = self::SCENARIO_CREATE;
        $create = $this->clientConfigFormat();
        $this->scenario = self::SCENARIO_UPDATE;
        $update = $this->clientConfigFormat();
        if ($create == $update)
            return ['forms' => ['default' => $create]];
        return ['forms' => ['create' => $create, 'update' => $update]];
    }

    public function getRuleConfig()
    {
        $validators = [];
        foreach ($this->activeAttributes() as $attribute) {
            foreach ($this->getActiveValidators($attribute) as $validator) {
                $validators[$attribute][$validator->validatorID()] = $validator->getRuleConfig($this, $attribute);
            }
        }
        return $validators;
    }
    // --------------------------------------------------------------------

    /**
     * mendapatkan default value untuk form field dari url query
     * @param string $attribute
     * @return string|null
     */
    public function queryDefaultValue($attribute)
    {
        $t = $this->getIsNewRecord() ? self::SCENARIO_CREATE : self::SCENARIO_UPDATE;
        $defaultValueQuery = $t . '-' . static::searchClassName();
        if ($query = Kukusa::$app->getRequest()->getQueryParam($defaultValueQuery)) {
            if (isset($query[$attribute])) {
                $this->{$attribute} = $query[$attribute];
                return $query[$attribute];
            }
        }
        return null;

    }

    /**
     * di panggil pada setup console
     */
    public static function onSetup()
    {
    }

    /**
     * set detault Model options
     * @return ModelOptions
     */
    abstract protected function _options();

    // Model Repo --------------------------------------------------------------------

    /** @var string base url untuk model repo */
    public static $modelRepoUrl;

    /**
     * init saat model di panggil dari model repo
     * @param string $module
     * @param string $modelKey
     */
    public static function init_model_repo($module, $modelKey)
    {
        static::$modelRepoUrl = Kukusa::$app->modelRepo->prefixUri . '/' . $module . '/' . $modelKey;
    }

    /**
     * mendapatkan url model repo
     * @param string $path
     * @param array $params
     * @return string
     */
    public static function modelRepoUrl($path = '', $params = [])
    {
        $params[0] = '/' . static::$modelRepoUrl . '/' . ltrim($path, '/');
        return Url::to($params);
    }

    /**
     * mendapatkan url model repo __rest
     * @param string $path
     * @param array $params
     * @return string
     */
    public static function modelRepoUrlRest($path = '', $params = [])
    {
        $path = '__rest/' . ltrim($path, '/');
        return static::modelRepoUrl($path, $params);
    }

    /**
     * mendapatkan url model repo path, tanpa dir
     * @param string $path
     * @param array $params
     * @return string
     */
    public static function modelRepoUrlPath($path = '')
    {
        $path = $path ? '/' . ltrim($path, '/') : '';
        return static::$modelRepoUrl . $path;
    }

    /**
     * actions list, dipanggil juga pada saat setup, dan di tambahkan pada RBAC
     * @return array
     */
    public static function modelRepoActions()
    {
        return [
            self::$ACTION_INDEX => [],
            self::$ACTION_CREATE => [],
            self::$ACTION_UPDATE => [],
            self::$ACTION_DELETE => [],
            self::$ACTION_IMPORT => [],
            self::$ACTION_EXPORT => [],
        ];
    }

    /**
     * list role untuk actions, jika '*' maka memiliki akses ke semua actions,
     * ex untuk custom role dan action:
     * ```php
     *  return [
     *      RbacHelper::roleRoleName('operator') => ['index','update']
     *  ]
     * ```
     * @return array
     */
    public static function modelRepoRoles()
    {
        return [
            RbacHelper::roleRoleName(RbacHelper::ROLE_SUPERUSER) => '*'
        ];
    }
    // --------------------------------------------------------------------

}