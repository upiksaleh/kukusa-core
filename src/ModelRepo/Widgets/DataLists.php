<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Widgets;


use Kukusa;
use Kukusa\Base\Widget;
use Kukusa\Grid\ActionColumn;
use Kukusa\Grid\GridView;
use Kukusa\Grid\SerialColumn;
use Kukusa\Helpers\ArrayHelper;
use Kukusa\Helpers\Url;
use Kukusa\Widgets\LinkPager;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class DataLists extends Widget
{
    /** @var Kukusa\Base\BaseModelController */
    private $_model;
    private $_modelClass;
    /**
     * @var Kukusa\Base\ModelOptions
     */
    private $_modelOptions;
    /**
     * @var Kukusa\Base\ModelRepo
     */
    protected $modelRepo;

    public $repoModule;
    public $repoModel;
    /**
     * @var array|GridView
     */
    public $gridView = [];
    /**
     * @var array|ActiveDataProvider
     */
    public $dataProvider = [];
    /** @var array */
    public $gridColumns = [];
    /**
     * @var false|array|ActionColumn
     */
    public $gridActionColumn = [];

    /**
     * @var false|array|SerialColumn
     */
    public $gridSerialColumn = [];
    /**
     * posisi action column, L|R
     * @var string
     */
    public $gridActionColumnPosition = 'L';

    public function init()
    {
        parent::init();
        $this->modelRepo = Kukusa::$app->modelRepo;
        if (!$this->repoModule || !$this->repoModel) throw new InvalidConfigException('"repoModule" dan "repoModel" harus di set.');
        $this->initModel();
        $this->init_gridView();
        $this->initGridColumns();
    }

    private function initModel()
    {
//        print_r($this->modelRepo->getCurrentModel());exit;
        if (!$this->modelRepo->exist($this->repoModule, $this->repoModel))
            throw new NotFoundHttpException(Kukusa::t('kukusa', 'Page not found.'));
        $this->_modelClass = $this->modelRepo->getModelClassName($this->repoModule, $this->repoModel);
        $this->_model = $this->modelRepo->getModelObj($this->repoModule, $this->repoModel);
        $this->_modelOptions = $this->_model->modelOptions;
//        $this->modelRepo->setCurrent($this->repoModule, $this->repoModel);
    }

    public function run()
    {
        echo $this->renderGrid();
    }

    public function init_gridView()
    {
        if (is_array($this->dataProvider))
            $this->_modelOptions->set('dataProvider', ArrayHelper::merge([
                'class' => 'yii\data\ActiveDataProvider',
                'query' => $this->_modelClass::find()
            ], $this->dataProvider));
        else
            $this->_modelOptions->set('dataProvider', $this->dataProvider);
        $this->dataProvider = $this->_modelOptions->dataProvider;


        $this->_model->initDataProvider($this->dataProvider);

        if (is_array($this->gridView)) {
            $this->_modelOptions->set('gridView', ArrayHelper::merge([
                'class' => 'Kukusa\Grid\GridView',
                'dataProvider' => $this->dataProvider,
                'filterModel' => $this->_model->filterModel,
//                'layout' => Html::tag('div', '{items}', ['class' => 'table-responsive']),
                'showFooter' => true,
//                'columns' => [
//                    ['class'=>ActionColumn::class]
//                ],
                'pager' => [
                    'class' => LinkPager::class,
                    'lastPageLabel' => '&raquo;&raquo;',
                    'firstPageLabel' => '&laquo;&laquo;'
                ],
            ], $this->gridView));
        } else {
            $this->_modelOptions->set('gridView', $this->gridView);
        }
        $this->gridView = $this->_modelOptions->gridView;
    }

    private function initGridColumns()
    {
        if (is_array($this->gridActionColumn)) {
            $this->_modelOptions->set('gridActionColumn', ArrayHelper::merge([
                'class' => ActionColumn::class,
                'grid' => $this->gridView,
                'header' => Kukusa::t('kukusa', 'Aksi'),
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($this->_modelOptions->useLinkEncoded)
                        $key = Url::safe_b64encode($key);
                    return $this->_model::modelRepoUrl($action . '/' . $key);
                }
            ], $this->gridActionColumn));
        } else
            $this->_modelOptions->set('gridActionColumn', $this->gridActionColumn);

        $this->gridActionColumn = $this->_modelOptions->gridActionColumn;
        if ($this->gridActionColumn !== false) {
            if ($this->gridActionColumnPosition === 'L')
                array_unshift($this->gridView->columns, $this->gridActionColumn);
            else array_push($this->gridView->columns, $this->gridActionColumn);
        }

        if (is_array($this->gridSerialColumn))
            $this->_modelOptions->set('gridSerialColumn', ArrayHelper::merge([
                'class' => SerialColumn::class,
                'grid' => $this->gridView,
                'header' => '#'
            ], $this->gridSerialColumn));
        else
            $this->_modelOptions->set('gridSerialColumn', $this->gridSerialColumn);
        $this->gridSerialColumn = $this->_modelOptions->gridSerialColumn;
        if ($this->gridSerialColumn !== false)
            array_unshift($this->gridView->columns, $this->gridSerialColumn);
    }

    public function renderGrid()
    {
        return $this->gridView->run();
    }
    // --------------------------------------------------------------------

}