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
use Kukusa\Helpers\Url;
use yii\web\ServerErrorHttpException;

class CreateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    /**
     * @var string the name of the view action. This property is needed to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';


    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {

//        if ($this->checkAccess) {
//            call_user_func($this->checkAccess, $this->id);
//        }

        /* @var $model Kukusa\Base\BaseModelController */
        $model = new $this->modelClass();

        $model->addScenario($this->scenario, []);
        $model->scenario = $this->scenario;
        $model->load(Kukusa::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Kukusa::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute([$this->viewAction, 'id' => $id], true));
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }
}