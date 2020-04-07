<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Grid;


use Kukusa;
use Kukusa\Helpers\Html;
use Kukusa\Widgets\Modal;
use Kukusa\Widgets\ModalDelete;

class ActionColumn extends \yii\grid\ActionColumn
{
    public $useModelDelete = false;
    public $headerOptions = ['class' => 'action-column-header text-center'];
    public $contentOptions = ['class' => 'action-column-body text-center'];
    public $footerOptions = ['class'=>'action-column-footer text-center'];

    public function init()
    {
        parent::init();
        $this->grid->options['data-action-column']='';
        if ($this->useModelDelete) {
            echo ModalDelete::widget([
                'id' => 'modal-delete',
                'clientEvents' => [
                    'show.bs.modal' => 'function(event){alert(11)}'
                ]
            ]);
        }
    }

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons()
    {
        $this->initDefaultButton('view', 'eye');
        $this->initDefaultButton('update', 'pencil');
        $this->initDefaultButton('delete', 'trash');
    }

    /**
     * Initializes the default button rendering callback for single button.
     * @param string $name Button name as it's written in template
     * @param string $iconName The part of Bootstrap glyphicon class that makes it unique
     * @param array $additionalOptions Array of additional options
     * @since 2.0.11
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if (!isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false) {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                $classColor = '';
                switch ($name) {
                    case 'view':
                        $title = Kukusa::t('yii', 'View');
                        break;
                    case 'update':
                        $title = Kukusa::t('yii', 'Update');
                        break;
                    case 'delete':
                        $title = Kukusa::t('yii', 'Delete');
                        $classColor = 'text-danger';
                        break;
                    default:
                        $title = ucfirst($name);
                }
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);

                if($name === 'delete') {
                    if ($this->useModelDelete) {
                        $options = array_merge(Modal::modalAttribute('#modal-delete'), $options);
                    } else {
                        $options = array_merge([
                            'data-confirm' => Kukusa::t('kukusa', 'Anda yakin ingin menghapus item ini?'),
                            'data-method' => 'post',
                        ], $options);
                    }
                }
                if (!$iconName)
                    return Html::a($title, $url, $options);
                $icon = Html::icon($iconName, ['class' => $classColor, 'tag' => 'i']);
                return Html::a($icon, $url, $options);
                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-$iconName"]);
                return Html::a($icon, $url, $options);
            };
        }
    }
}