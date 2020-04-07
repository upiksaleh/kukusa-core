<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use Kukusa\Extensions\Select2\Select2;
use Kukusa\Grid\GridView;
use Kukusa\Helpers\Html;
use Kukusa\Html\Button;
use Kukusa\Widgets\BoxCard;
use yii\data\ArrayDataProvider;
use yii\web\JsExpression;

/** @var string $role */
$this->title = Kukusa::t('kukusa', 'Pengguna dalam peran: {role}', ['role' => $role]);
$authManager = Kukusa::$app->getAuthManager();
$model = [];
foreach ($authManager->getUserIdsByRole($role) as $userId) {
    $userInfo = \Kukusa\Models\SysUser::find()->select(['username'])->where(['id' => $userId])->one();
    $model[] = [
        'id' => $userId,
        'username' => $userInfo->username
    ];
}
$dataProvider = new ArrayDataProvider([
    'allModels' => $model,
    'sort' => [
//        'attributes' => ['name', 'description', 'ruleName', 'data', 'createdAt', 'updatedAt']
    ],
    'pagination' => [
        'pageSize' => 0
    ]
]);
echo Html::beginForm();
BoxCard::begin([
    'title' => Kukusa::t('kukusa', 'Tetapkan Pengguna'),
    'tools_order' => [],
    'footer' => true,
    'footerContent' => Button::widget(['tag' => 'a','label' => Kukusa::t('kukusa','Kembali'),'options' => ['href'=>\Kukusa\Helpers\Url::to(['/system/roles/roles'])]]) .' '. Button::widget([
        'label' => Kukusa::t('kukusa', 'Tetapkan Pengguna'),
        'type' => 'primary'
    ])
]);
$allPermissions = $authManager->getPermissions();

foreach ($model as $name => $permission) {
    if (isset($allPermissions[$name]))
        unset($allPermissions[$name]);
}
echo \Kukusa\ModelRepo\RestSelect2::widget([
    'name' => 'assign-user',
    'repoModule' => 'system',
    'repoModel' => 'sys-users',
    'fields' => "id,username,group",
    'templateResult' => 'return data.id+" - "+data.username',
    'filterData' => [
        'and' => [
            ['username' => ['like' => '{{term}}']]
        ]
    ],
    'options' => [
        'multiple'=>true
    ]
]);
BoxCard::end();
echo Html::endForm();
BoxCard::begin();

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'label' => 'Delete',
            'headerOptions' => ['class' => 'text-center', 'style' => 'width:100px'],
            'contentOptions' => ['class' => 'text-center'],
            'format' => 'raw',
            'value' => function ($model) use ($authManager) {
                return Html::beginForm() . Html::hiddenInput('delete-assign', $model['id']) . Button::widget(['encodeLabel' => false, 'size' => Button::SIZE_XS, 'label' => Html::icon('trash')]) . Html::endForm();
            }
        ],
        [
            'attribute' => 'id',
            'label' => Kukusa::t('kukusa', 'ID Pengguna')
        ],
        'username',
    ]
]);
BoxCard::end();