<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use Kukusa\Helpers\Html;

/** @var \Kukusa\Web\View $this */
/** @var array $_params */
/** @var string $repoModule */
/** @var string $repoModel */
/** @var string $modelClass */
/** @var \Kukusa\Base\BaseModelController $model */
/** @var \Kukusa\Base\ModelOptions $modelOptions */

/** @var string $type type form */
//$this->title = $modelOptions->title;
$_params['_type'] = 'index';
$this->beginContent('@kukusa/views/model-repo/_base.php', $_params);
$saveBtn = Html::submitButton(Html::icon('save') . ' ' . $type,
    ['class' => ['btn', 'btn-success']]
);
$cancelBtn = Html::a(Html::icon('undo') . ' ' . Kukusa::t('kukusa', 'Batal'),
    'index',
    ['class' => ['btn', 'btn-default']]
);
$form = \Kukusa\Form\BuildFormWidget::begin([
    'type' => $type,
    'model' => $model,
    'renderFields' => false,
    'showButtons' => false
]);
\Kukusa\Widgets\BoxCard::begin([
    'type' => 'primary',
    'title' => $type,
    'footer' => true,
    'footerContent' => $form->renderButtons()
]);
echo $form->renderFields();

\Kukusa\Widgets\BoxCard::end();
\Kukusa\Form\BuildFormWidget::end();

$this->endContent();
