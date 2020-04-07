<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Rest;


use Kukusa;
use Kukusa\Base\Model;
use Kukusa\Rest\Controller;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ActiveController extends Controller
{
    /**
     * @var string the scenario used for updating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $updateScenario = Model::SCENARIO_UPDATE;
    /**
     * @var string the scenario used for creating a model.
     * @see \yii\base\Model::scenarios()
     */
    public $createScenario = Model::SCENARIO_CREATE;

    /**
     * @var \Kukusa\Base\ModelRepo
     */
    protected $modelRepo;
    /** @var string|\Kukusa\Base\BaseModelController */
    protected $modelClass;
    /** @var \Kukusa\Base\BaseModelController */
    protected $model;
    /** @var \Kukusa\Base\ModelOptions */
    protected $modelOptions;

    public $repoModule;
    public $repoModel;

    public function init()
    {
        parent::init();
        $this->modelRepo = Kukusa::$app->modelRepo;
        if ($this->repoModule && $this->repoModel) {
            if (!$this->modelRepo->exist($this->repoModule, $this->repoModel))
                throw new \Kukusa\Web\NotFoundHttpException('Model Repo key tidak ada.');
            $this->modelRepo->setCurrent($this->repoModule, $this->repoModel);
        } else {
            $this->modelRepo->setCurrentFromQueryParams();
            $this->repoModule = $this->modelRepo->currentModuleName;
            $this->repoModel = $this->modelRepo->currentModelName;
        }
        $this->modelClass = $this->modelRepo->currentModelClassName;
        $this->model = $this->modelRepo->currentModel;
        if (!$this->model && !$this->modelClass)
            throw new NotFoundHttpException();

        $this->modelOptions = $this->model->modelOptions;

        $this->serializer = $this->modelOptions->restSerializer;
    }
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => 'Kukusa\ModelRepo\Rest\IndexAction',
                'repoModule' => $this->repoModule,
                'repoModel' => $this->repoModel
            ],
            'view' => [
                'class' => 'yii\rest\ViewAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => 'Kukusa\ModelRepo\Rest\CreateAction',
//                'modelClass' => $this->modelClass,
//                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => 'yii\rest\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => 'yii\rest\DeleteAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'POST', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * Checks the privilege of the current user.
     *
     * This method should be overridden to check whether the current user has the privilege
     * to run the specified action against the specified data model.
     * If the user does not have access, a [[ForbiddenHttpException]] should be thrown.
     *
     * @param string $action the ID of the action to be executed
     * @param object $model the model to be accessed. If null, it means no specific model is being accessed.
     * @param array $params additional parameters
     * @throws ForbiddenHttpException if the user does not have access
     */
    public function checkAccess($action, $model = null, $params = [])
    {
    }
}