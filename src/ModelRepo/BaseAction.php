<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo;


use Kukusa;
use Kukusa\Base\Action;
use Kukusa\Web\NotFoundHttpException;

class BaseAction extends Action
{

    /**
     * @var \Kukusa\Base\ModelRepo
     */
    protected $modelRepo;
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
                throw new NotFoundHttpException('Model Repo key tidak ada.');
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

}