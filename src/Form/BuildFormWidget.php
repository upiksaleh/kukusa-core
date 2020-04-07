<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Form;


use Kukusa;
use Kukusa\Base\BaseModelController;
use Kukusa\Base\Widget;
use Kukusa\Helpers\Html;
use Kukusa\Html\Grid;
use Kukusa\Widgets\ActiveForm;
use yii\base\InvalidConfigException;

/**
 * Class BuildFormWidget
 * @package Kukusa\Form
 * @property string|array $goBackUrl
 */
class BuildFormWidget extends Widget
{
    /**
     * type create|update
     * @var string
     */
    public $type;
    /** @var BaseModelController */
    public $model;
    private $_fields;
    private $_fieldsRow;
    /** @var ActiveForm */
    private $_form;

    private $_buildFields = [];
    /**
     * @var bool
     */
    public $renderFields = true;
    /**
     * url untuk kembali
     * @var string|array
     */
    private $_goBackUrl;
    public $showButtons = true;
    public $showSubmitButton = true;
    public $showCancelButton = true;

    public function init()
    {
        if (!$this->model) throw new InvalidConfigException('Model harus di set.');
        $this->_fields = $this->model->normalizeFormFields;

        $this->_fieldsRow = $this->model->form_fields_row();
        $this->_form = ActiveForm::begin([
        ]);
        if (!empty($this->_fieldsRow)) {
            $htmlGrid = Grid::begin([]);
            foreach ($this->_fieldsRow as $rowSize => $fields) {
                $htmlGrid->beginCol(explode(',', $rowSize));
                $this->eachField($fields);
                $htmlGrid->endCol();
            }
            Grid::end();
        } else {
            $this->eachField($this->_fields);
        }
    }

    public function run()
    {
        if($this->renderFields)
            echo $this->renderFields();
        if ($this->showButtons)
            echo $this->renderButtons();
        ActiveForm::end();
    }

    public function renderFields()
    {
        return implode("\n", $this->_buildFields);
    }
    public function renderButtons()
    {
        $buttons = [];
        if ($this->showSubmitButton)
            $buttons[] = $this->renderSubmitButton();
        if ($this->showCancelButton)
            $buttons[] = $this->renderCancelButton();
        return implode(' ', $buttons);
    }

    public function renderSubmitButton()
    {
        return Html::submitButton(Html::icon('save') . ' ' . $this->type,
            ['class' => ['btn', 'btn-success']]
        );
    }

    public function renderCancelButton()
    {
        return Html::a(Html::icon('undo') . ' ' . Kukusa::t('kukusa', 'Batal'),
            $this->goBackUrl,
            ['class' => ['btn', 'btn-default']]
        );
    }

    private function eachField($fields)
    {
        foreach ($fields as $field) {
            $this->_buildFields[] = $this->buildField($field);
        }
    }

    /**
     * @param Field $field
     * @return Kukusa\Widgets\ActiveField
     */
    private function buildField($field)
    {
        if (is_string($field))
            $field = $this->_fields[$field];

            return $field->run($this->model, $this->_form);
            $build = $this->_form->field($this->model, $field->name);
            $field->activeField($build);
        return $build;
    }
    // --------------------------------------------------------------------
    /**
     * @return array|string
     */
    public function getGoBackUrl()
    {
        if(!$this->_goBackUrl)
            return $this->model::modelRepoUrl();
        return $this->_goBackUrl;
    }

    /**
     * @param array|string $goBackUrl
     */
    public function setGoBackUrl($goBackUrl)
    {
        $this->_goBackUrl = $goBackUrl;
    }

}