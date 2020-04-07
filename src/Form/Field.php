<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Form;


use Kukusa\Base\BaseModelController;
use Kukusa\Widgets\ActiveField;
use Kukusa\Widgets\ActiveForm;
use yii\base\BaseObject;

class Field extends BaseObject
{
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const PASSWORD = 'password';
    const HIDDEN = 'hidden';
    const NUMBER = 'number';
    const DISABLED = 'disabled';
    const FIELD_INFO = 'field_info';

    /**
     * @var string
     */
    public $name;
    /**
     * @var string custom label
     */
    public $label;

    public $type = self::TEXT;

    public $attribute = [];

    public $options = [];
    /**
     * active field attribute
     * @var array|ActiveField
     */
    public $activeField = [];

    /**
     * @param ActiveField $field
     * @return Field
     */
    private function activeField(&$field)
    {
        foreach ($this->activeField as $key => $value)
            if ($field->canSetProperty($key))
                $field->{$key} = $value;
        return $this;
    }

    /**
     * @param BaseModelController $model
     * @param ActiveForm $form
     * @return ActiveField
     */
    public function run($model, $form)
    {
        $field = $form->field($model, $this->name, $this->options);
        $this->activeField($field);
        if ($this->label)
            $field->label($this->label);
        switch ($this->type) {
            case self::PASSWORD:
                $field = $field->passwordInput($this->attribute);
                break;
            case self::HIDDEN:
                $field = $field->hiddenInput($this->attribute);
                break;
            default:
                $field = $field->textInput($this->attribute);
        }
        return $field;
    }

    /**
     * @param $name
     * @param array|Field $config
     * @return static
     */
    public static function new($name, $config = [])
    {
        $config['name'] = $name;
        return new static($config);
    }


}