<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

/** @var $this \yii\web\View */
use yii\widgets\ActiveForm;

$a = new Kukusa\Models\DataModel();
$a->loadDefaultValues();
//echo $a->data;
//print_r($a->validators);
$validators = [];
//foreach ($a->getActiveValidators('nama') as $validator) {
//    /* @var $validator \yii\validators\Validator */
//    $js = $validator->clientValidateAttribute($a, 'data', $this);
//    if ($validator->enableClientValidation && $js != '') {
//        if ($validator->whenClient !== null) {
//            $js = "if (({$validator->whenClient})(attribute, value)) { $js }";
//        }
//        $validators[] = $js;
//    }
//}
print_r($validators);
$form = ActiveForm::begin([

    'enableAjaxValidation' => true,
]);
echo $form->field($a, 'nama');
print_r($form->attributes);
echo $form->field($a, 'hp');
ActiveForm::end();
$this->endBody();
$this->endPage();
