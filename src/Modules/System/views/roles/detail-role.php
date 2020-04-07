<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use Kukusa\Helpers\Html;
use Kukusa\Helpers\Url;
use Kukusa\Html\Button;
use Kukusa\Html\Grid;
use Kukusa\Modules\System\Models\AddRoleForm;
use Kukusa\Widgets\ActiveForm;
use Kukusa\Widgets\BoxCard;
use yii\rbac\Item;

/** @var AddRoleForm $model */
if ($model->isUpdating)
    $this->title = Kukusa::t('kukusa', 'Perbarui peran kustom');
else
    $this->title = Kukusa::t('kukusa', 'Tambah peran kustom');
$formDelete = '';
if ($model->isUpdating) {
    $formDelete = Html::beginForm() . Html::hiddenInput('_delete', $model->name) . Button::widget(['label' => Kukusa::t('kukusa', 'Hapus peran'), 'type' => 'danger']) . Html::endForm();
}
$htmlGrid = Grid::begin();
echo $htmlGrid->beginCol(['md-6']);
BoxCard::begin([
    'title' => $formDelete,
    'tools_order' => []


]);
$form = ActiveForm::begin([]);
echo $form->field($model, 'name');
echo $form->field($model, 'description')->textarea();
echo Button::widget([
        'label' => $model->isUpdating ? Kukusa::t('kukusa', 'Perbarui') : Kukusa::t('kukusa', 'Tambah'),
        'type' => 'primary'
    ]) . ' ';
echo Button::widget([
    'label' => Kukusa::t('kukusa', 'Batal'),
    'tag' => 'a',
    'options' => [
        'href' => Url::to(['roles'])
    ]
]);
ActiveForm::end();
BoxCard::end();
$htmlGrid->endCol();
Grid::end();
