<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Base;

use Kukusa;
use Kukusa\Data\ActiveDataFilter;
use Kukusa\Grid\ActionColumn;
use Kukusa\Grid\GridView;
use Kukusa\Helpers\ArrayHelper;
use yii\base\BaseObject;
use yii\data\ActiveDataProvider;
use yii\data\BaseDataProvider;

/**
 * Class ModelOptions
 * @package Kukusa\Base
 * @property ActiveDataFilter $activeDataFilter
 * @property ActiveDataProvider $dataProvider
 * @property \yii\rest\Serializer $restSerializer
 * @property GridView $gridView
 * @property false|ActionColumn $gridActionColumn
 * @property false|Kukusa\Grid\SerialColumn $gridSerialColumn
 */
class ModelOptions extends BaseObject
{
    public $title;
    /**
     * untuk filter pada grid view
     * @var array
     */
    public $filterRules = [];

    /**
     * menggunakan link encoded pada key datalist
     * @var bool
     */
    public $useLinkEncoded = false;

    /**
     * @var false|array|ActionColumn
     */
    private $__gridActionColumn = [
        'class' => ActionColumn::class
    ];
    /**
     * @var false|array|Kukusa\Grid\SerialColumn
     */
    private $__gridSerialColumn = [
        'class' => 'Kukusa\Grid\SerialColumn'
    ];

    private $__gridView = [
        'class' => 'Kukusa\Grid\GridView'
    ];

    private $__restSerializer = [
        'class' => 'Kukusa\Rest\Serializer',
        'collectionEnvelope' => 'items'
    ];
    /** @var array|ActiveDataProvider */
    private $__dataProvider = [
        'class' => 'yii\data\ActiveDataProvider',
    ];
    /**
     * @var false|array|ActiveDataFilter
     */
    private $__activeDataFilter = [
        'class' => ActiveDataFilter::class
    ];

    public function init()
    {
    }


    public function forceReturnObj($config)
    {
        if (is_array($config))
            return Kukusa::createObject($config);
        return $config;
    }

    public function set($name, $value)
    {
        $this->__set($name, $value);
    }

    public function __set($name, $value)
    {
        if ($this->hasProperty('__' . $name)) {
            if (is_array($value)) {
                if (is_array($this->{'__' . $name}))
                    $this->{'__' . $name} = ArrayHelper::merge($this->{'__' . $name}, $value);
                else {
                    Kukusa::setObjectValues($this->{'__' . $name}, $value);
                }
            } else
                $this->{'__' . $name} = $value;
        } else
            parent::__set($name, $value);
    }

    public function __get($name)
    {
        if ($this->hasProperty('__' . $name))
            return $this->forceReturnObj($this->{'__' . $name});
        else
            return parent::__get($name);
    }

}