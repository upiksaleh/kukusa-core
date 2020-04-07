<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Rest;


use Kukusa\Models\SysGroups;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\DataFilter;
use yii\helpers\StringHelper;

class IndexAction extends Action
{
    /**
     * @var DataFilter|null data filter to be used for the search filter composition.
     * You must setup this field explicitly in order to enable filter processing.
     * For example:
     *
     * ```php
     * [
     *     'class' => 'yii\data\ActiveDataFilter',
     *     'searchModel' => function () {
     *         return (new \yii\base\DynamicModel(['id' => null, 'name' => null, 'price' => null]))
     *             ->addRule('id', 'integer')
     *             ->addRule('name', 'trim')
     *             ->addRule('name', 'string')
     *             ->addRule('price', 'number');
     *     },
     * ]
     * ```
     *
     * @see DataFilter
     *
     * @since 2.0.13
     */
    public $dataFilter;


    /**
     * @return ActiveDataProvider
     */
    public function run()
    {
//        if ($this->checkAccess) {
//            call_user_func($this->checkAccess, $this->id);
//        }

        return $this->prepareDataProvider();
    }

    /**
     * Prepares the data provider that should return the requested collection of the models.
     * @return ActiveDataProvider
     */
    protected function prepareDataProvider()
    {

        $requestParams = Yii::$app->getRequest()->getBodyParams();

        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        $this->dataFilter = $this->modelOptions->activeDataFilter;
        if($this->model->filterModel)
            $this->dataFilter->searchModel = $this->model->filterModel;
        $filter = null;
        if ($this->dataFilter !== null) {
            if ($this->dataFilter->load($requestParams)) {
//                print_r($this->dataFilter);exit;
                $filter = $this->dataFilter->build();
                if ($filter === false) {
                    return $this->dataFilter;
                }
            }
        }
//        print_r($this->dataFilter->fields());exit;
        /* @var $modelClass \yii\db\BaseActiveRecord */
        $modelClass = $this->modelClass;

        $query = $modelClass::find();
        if (!empty($filter)) {
            $query->andWhere($filter);
        }
        $this->modelOptions->__set('dataProvider', [
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'pagination' => [
                'params' => $requestParams,
            ],
            'sort' => [
                'params' => $requestParams,
            ],
        ]);
//        for($i=0;$i<20;$i++){
//            $a= new SysGroups();
//            $a->name = rand(1111, 99999);
//            $a->class = rand(1111, 99999);
//            $a->save(false);
//        }
//        print_r($this->modelOptions->dataProvider->pagination->getPageSize());exit;
        return $this->modelOptions->dataProvider;
    }
}