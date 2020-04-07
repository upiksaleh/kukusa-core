<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Actions;


use Kukusa;

class RestAction extends Action
{
    public function init()
    {
        parent::init();
        $this->controller->enableCsrfValidation = false;
        if (!isset(Kukusa::$app->request->parsers['application/json']))
            Kukusa::$app->request->parsers['application/json'] = 'yii\web\JsonParser';
    }
    public function run($restAction = '__', $id= '')
    {
        $restConfig = [
//            'modelClass' => $this->modelClass,
//            'model' => $this->model,
//            'modelOptions' => $this->modelOptions,
            'repoModule'=>$this->repoModule,
            'repoModel'=>$this->repoModel
//            'backendControllerId' => $this->controller->getUniqueId()
        ];

        $restId = $this->controller->id . '/__rest';
        $activeController = new Kukusa\ModelRepo\Rest\ActiveController($restId, $this->controller->module, $restConfig);
        return $activeController->runAction($restAction, ['id' => $id]);
    }

}