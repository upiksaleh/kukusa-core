<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Actions;


use Kukusa;
use Kukusa\Web\NotFoundHttpException;

class Action extends Kukusa\ModelRepo\BaseAction
{
    public $viewFile = '@kukusa/views/model-repo/index';


    protected $viewParams = [];

    /**
     * url untuk kembali setelah berhasil menambah item
     * @var string|array
     */
    public $goBackUrl;
    public function init()
    {
        parent::init();
        if (!$this->goBackUrl) $this->goBackUrl = $this->model::modelRepoUrl();
    }

    protected function viewParams($merge = [])
    {
        $params = array_merge($this->viewParams, $merge, [
            'modelRepo' => $this->modelRepo,
            'modelClass' => $this->modelClass,
            'model' => $this->model,
            'modelOptions' => $this->modelOptions,
            'repoModule' => $this->repoModule,
            'repoModel' => $this->repoModel
        ]);
        // dipakai untuk sub view
        $params['_params'] = $params;
        return $params;
    }
    protected function findModel($params)
    {
        if (($model = $this->model::findOne($params)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException();
    }
    /**
     * @param $params
     * @return string|Kukusa\Base\BaseModelController|null
     * @throws NotFoundHttpException
     */
    protected function findModelDelete($params)
    {
        if (!empty($this->modelOptions->mergeDeleteParams))
            $params = array_merge($params, $this->modelOptions->mergeDeleteParams);
        $model = $this->modelClass;
        if (($model = $model::findOne($params)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}