<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use Kukusa\Helpers\Html;
use Kukusa\Widgets\ActiveForm;
use Kukusa\Widgets\ICheck;

$this->title = Kukusa::$app->name;
$this->registerCss('
body{background: #d2d6de;}
.login-header-text{
    border-bottom: 1px solid #ddd;
    width: 100%;
    background: #ffffff;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    padding: 10px 5px;
}
');
$appAsset = Kukusa::registerAppAsset($this);
?>
<div class="login-box">
    <div class="login-logo">
        <img src="<?= $appAsset->getLogoUrl() ?>" style="width:100px;"/>
    </div>
    <div class="login-header-text">
        <?=$header_text?></div>
    <div class="login-box-body">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
        ]); ?>

        <?= $form->field($model, 'username', [
            'options'=>['class'=>'form-group has-feedback'],
            'template' => ($show_label_input ? '{label}':'').'{input}{error}<span class="fal fa-user form-control-feedback"></span>'])->textInput(['autofocus' => true, 'placeholder' => $placeholder_user]) ?>

        <?= $form->field($model, 'password',[
            'options'=>['class'=>'form-group has-feedback'],
            'template' => ($show_label_input ? '{label}':'').'{input}{error}<span class="fal fa-key form-control-feedback"></span>'])->passwordInput(['placeholder'=>$placeholder_pass]) ?>
        <div class="row">
            <div class="col-xs-8">
                <?= $show_remember_checkbox ? $form->field($model, 'rememberMe', ['template'=>'{input}'])->widget(ICheck::class,[
                    'color' => 'green',
                    'skin' => ICheck::SKIN_FLAT,
                ]) : ''?>
            </div>
            <div class="col-xs-4">
                <?= Html::submitButton(Kukusa::t('kukusa','Masuk'), ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>



