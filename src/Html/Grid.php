<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Html;


use Kukusa\Base\Widget;
use Kukusa\Helpers\Html;

class Grid extends Widget
{
    public static $autoIdPrefix = 'grid-row-';

    public $rowClass = 'row';
    public $colPrefix = 'col-';
    public $columns = [];

    public function init()
    {
        parent::init();
        if (isset($this->options['class']))
            $this->options['class'] = $this->rowClass . ' ' . $this->options['class'];
        else {
            $this->options['class'] = $this->rowClass;
        }
        echo Html::beginTag('div', $this->options);
        if(!empty($this->columns)){
            foreach($this->columns as $column){
                $colOptions = isset($column[2]) ? $column[2] : [];
                $this->beginCol($column[0], $colOptions);
                echo $column[1];
                $this->endCol();
            }
        }
    }

    public function run()
    {
        echo Html::endTag('div');
    }

    /**
     * @param array|string $cols
     * @param array $options
     */
    public function beginCol($cols = [], $options = [])
    {
        if(is_string($cols))
            $cols = [$cols];
        $class = [];
        foreach ($cols as $col) {
            $class[] = $this->colPrefix . $col;
        }
        Html::addCssClass($options, $class);
        echo Html::beginTag('div', $options);
    }

    public function endCol()
    {
        echo Html::endTag('div');
    }
}