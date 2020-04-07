<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Controllers;


use Kukusa;
use Kukusa\Rbac\RbacHelper;
use Kukusa\Web\Controller;
use yii\filters\ContentNegotiator;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ModelRepoController extends Controller
{
    public $layout = '@kukusa/views/_layouts/backend';

    /**
     * @var \Kukusa\Base\ModelRepo
     */
    protected $modelRepo;
    /** @var string|Kukusa\Base\BaseModelController */
    private $modelClass;
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
        }else{
            $this->modelRepo->setCurrentFromQueryParams();
            $this->repoModule = $this->modelRepo->currentModuleName;
            $this->repoModel = $this->modelRepo->currentModelName;
        }
        $this->modelClass = $this->modelRepo->currentModelClassName;
        $this->model = $this->modelRepo->currentModel;
        if (!$this->model && !$this->modelClass)
            throw new NotFoundHttpException();

        $this->modelOptions = $this->model->modelOptions;
    }

    public function behaviors()
    {
        $controller = $this->repoModule . '/' . $this->repoModel;
        $roleAction = $controller.'/'.$this->action->id;
        if($this->action->id === '__rest'){
            $roleAction = $controller.'/'.Kukusa::$app->request->getQueryParam('restAction');
        }
        return [
            'access' => [
                'class' => 'Kukusa\Filters\AccessControl',
//                'only' => array_keys($this->modelClass::modelRepoActions()),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [RbacHelper::menuRoleName($controller)],
                    ],
                    [
                        'allow' => true,
                        'actions' => [$this->action->id],
                        'roles' => [RbacHelper::menuRoleName($roleAction)],
                    ],
                ],
            ],

        ];
    }

    public function actions()
    {
        return [
            'index' => [
                'class' => 'Kukusa\ModelRepo\Actions\IndexAction',
            ],
            'create' => [
                'class' => 'Kukusa\ModelRepo\Actions\FormAction',
            ],
            'update' => [
                'class' => 'Kukusa\ModelRepo\Actions\FormAction',
                'type' => Kukusa\Base\ModelRepo::FORM_TYPE_UPDATE
            ],
            'view' => [
                'class' => 'Kukusa\ModelRepo\Actions\ViewAction',
            ],
            'delete' => [
                'class' => 'Kukusa\ModelRepo\Actions\DeleteAction',
            ],
            '__rest' => [
                'class' => 'Kukusa\ModelRepo\Actions\RestAction',
            ]
        ];
    }


    public function actionInit()
    {
        echo '1';
    }

    public function actionIndex2()
    {
        return $this->render('index', $this->viewParams());
    }

    public function actionUpdatea($id)
    {
        print_r(Kukusa::$app->request->queryParams);
//        echo Kukusa::$app->urlManager->createUrl(['ada','a'=>1]);
        echo $id;
    }

    public function actionCreate1()
    {
        return Kukusa\ModelRepo\Widgets\FormWidget::widget([
            'model' => Kukusa::$app->modelRepo->getModelObj($this->repoModule, $this->repoModel)
        ]);
    }
}