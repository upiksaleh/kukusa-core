<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo;


use Kukusa;
use Kukusa\Extensions\Select2\Select2;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\JsExpression;

class RestSelect2 extends Select2
{
    public $repoModule;
    public $repoModel;
    /** @var Kukusa\Base\BaseModelController */
    private $repoModelClassName;
    /** @var Kukusa\Base\BaseModelController */
    private $repoModelClassObj;

    /**
     * fields yang akan di ambil
     * @var string
     */
    public $fields = '';
    /**
     * data per request
     * @var int
     */
    public $perPage;
    /**
     * template data yang akan ditampilkan
     * @var string
     */
    public $templateResult = 'return data.id;';
    /**
     * data/field yang akan ditampilkan setelah dipilih. jika kosong, maka default adalah $templateResult
     * @var string
     */
    public $templateSelection;

    /**
     * menggunakan DataFilter
     * ```php
     *  [
     *      'and' =>[
     *          ['username' => ['like' => '{{term}}']]
     *      ]
     *  ]
     * atau
     * ```php
     * [
     *      'and',
     *      ['group'=>new JsExpression("function(){return $('#id-input').val();}")]
     * ]
     * @var array
     * @see ActiveRecord::prosesFiltering()
     */
    public $filterData = [];

    /**
     * term yang akan di replace pada query filterData
     * ```php
     *  [
     *      'field1' => new JsExpression("function(){return $('#id-input').val();}"),
     *      'field2' => ....
     *  ]
     *
     * kemudian pada filterData gunakan {{field1}}
     * @var array
     */
    public $termsReplace = [];
    /**
     * data relasi model yang akan digunakan.
     * @var array
     */
    public $expands = [];

    /**
     * digunakan untuk mengganti value "id" yang akan disimpan pada inputan.
     * ```php
     *  $dataId = new JsExpression("function(data){return data.uid;}")
     *```
     * @var string
     */
    public $dataID = 'null';

    public $promptText = '---';


    private $serializer;

    public function init()
    {
        if (!$this->repoModule || !$this->repoModel)
            throw new InvalidConfigException('repoModule dan repoModel tidak boleh kosong');
        $this->repoModelClassName = Kukusa::$app->modelRepo->getModelClassName($this->repoModule, $this->repoModel);
        if ($this->repoModelClassName === false) {
            throw new InvalidConfigException('ModelRepo class tidak ditemukan');
        }
        $this->repoModelClassObj = Kukusa::$app->modelRepo->getModelObj($this->repoModule, $this->repoModel);

        if (!$this->templateSelection)
            $this->templateSelection = $this->templateResult;

        $modelOptions = $this->repoModelClassObj->modelOptions;
        $this->serializer = $modelOptions->restSerializer;
        if (!$this->perPage) $this->perPage = $modelOptions->dataProvider->pagination->defaultPageSize;
        parent::init();
        $this->clientOptions = array_merge($this->clientOptions, $this->options());
    }

    public function options()
    {
        $filterData = Json::encode($this->filterData);
        $termsReplace = Json::encode($this->termsReplace);
        $promptText = Json::encode($this->promptText);
        $expands = implode(',', $this->expands);
        return [
            'ajax' => [
                'url' => $this->repoModelClassName::modelRepoUrlRest('index'),
                'type' => 'POST',
                'contentType' => 'application/json',
                'dataType' => 'json',
                'cache' => true,
                'placeholder' => "Select a state",
                'prompt' => "Select a state",
                'data' => new JsExpression("function (params) {
                    var fields = '{$this->fields}';
                    var filterData = {$filterData};
                    var termsReplace = {$termsReplace};
                    
                    var query = {
                        'fields': fields,
                        'per-page': {$this->perPage},
                        'expand': '{$expands}',
                        page: params.page || 1
                    }
                    if(!params.term) params.term = '';
                    if(filterData){
                        query['filter'] = filterData; 
                    }
                    query = JSON.stringify(query);
                    $.each(termsReplace, function(k,v){
                        query = query.replace(new RegExp('{{'+k+'}}', 'g'), v);
                    })
                    return query.replace(/{{term}}/g, params.term);
                }"),
                'processResults' => new JsExpression("function (_data, params) {
                    var dataId = {$this->dataID};
                    var items = _data.{$this->serializer->collectionEnvelope};
                    if(items && dataId !== null){
                        var data_ = $.map(items, function(obj){
                            obj.id = dataId(obj)
                            return obj;
                        });
                    }
                    return {
                      results: data_ || items,
                      pagination: {
                        more: _data.{$this->serializer->linksEnvelope}.next
                      }
     
                    };
                }"),
                'results' => new JsExpression("function(data, page){
                    var data = $.map(data, function (obj) {
                        obj.id = obj.uid || obj.id;
                            return obj;
                        });
                }")
            ],
            'templateResult' => new JsExpression("function(data) {
                if(data.loading)
                    return data.text;
                if(data.id==='') return {$promptText} ;
                {$this->templateResult}
            }"),
            'templateSelection' => new JsExpression("function (data) {
            if(data.id==='') return {$promptText} ;
                {$this->templateSelection}
            }"),
        ];
    }

}