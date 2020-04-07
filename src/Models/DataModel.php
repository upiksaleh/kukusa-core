<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Models;


use Kukusa;
use Kukusa\Base\BaseModelController;
use Kukusa\Form\Field;

class DataModel extends BaseModelController
{
    public static function tableName()
    {
        return '{{%data}}';
    }

    /**
     * Form fields
     * @return Field[]
     */
    public function form_fields()
    {
        return [
            new Field(['name' => 'nama']),
            new Field(['name' => 'hp', 'type' => Field::NUMBER]),
            new Field(['name' => 'alamat', 'type' => Field::TEXTAREA]),
        ];
    }
    public function form_fields_row()
    {
        return [
            'md-4,xl-12' => [
                'nama','hp'
            ],
            'md-8' => [
                'alamat'
            ]
        ];
    }

    public function rules()
    {
        return  [
            [['nama', 'hp'], 'required'],
            ['nama', 'string', 'max' => 20],
            ['hp', 'integer', 'max' => 14],
            ['alamat', 'safe'],
            ['alamat', 'default', 'value' => "ada'"],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function _options()
    {
        return new Kukusa\Base\ModelOptions([
            'title' => Kukusa::t('kukusa','Data Model'),
            'gridViewConfig' => [
            ]
        ]);
    }

}