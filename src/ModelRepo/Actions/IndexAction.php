<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Actions;


use Kukusa;

class IndexAction extends Action
{

    public $gridView = [];
    public $dataProvider = [];

    public function init()
    {
        parent::init();
    }

    public function init_gridView()
    {
//        if ($this->dataProvider)
//            $dataProvider = $this->dataProvider;
//        else {
//            $dataProvider = $this->modelOptions->gridDataProvider;
//        }
//        if ($this->gridViewConfig)
//            $gridViewConfig = $this->gridViewConfig;
//        else
//            $gridViewConfig = $this->modelOptions->gridViewConfig;
//        if (is_array($dataProvider))
//            $dataProvider = Kukusa::createObject(array_merge([
//                'class' => 'yii\data\ActiveDataProvider',
//                'query' => $this->modelClass::find()
//            ], $dataProvider));
//
//        $this->model->initDataProvider($dataProvider);
//        $gridViewConfig = array_merge([
//            'class' => 'Kukusa\Grid\GridView',
//            'dataProvider' => $dataProvider,
//            'filterModel' => $this->model->filterModel
//        ], $gridViewConfig);
//
//        $this->dataProvider = $dataProvider;
//        $this->gridViewConfig = $gridViewConfig;
    }

    public function run()
    {
        $this->init_gridView();
        return $this->controller->render($this->viewFile, $this->viewParams([
//            'repoModule' => $this->repoModule,
//            'repoModel' => $this->repoModel,
            'gridView' => $this->gridView,
            'dataProvider' => $this->dataProvider
        ]));
    }
}