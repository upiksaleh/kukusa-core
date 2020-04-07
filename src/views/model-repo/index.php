<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

use Kukusa\Helpers\Html;
use Kukusa\ModelRepo\Widgets\DataLists;

/** @var \Kukusa\Web\View $this */
/** @var \Kukusa\Base\ModelRepo $modelRepo */
/** @var array $_params */
/** @var string $repoModule */
/** @var string $repoModel */
/** @var string $modelClass */
/** @var \Kukusa\Base\BaseModelController $model */
/** @var \Kukusa\Base\ModelOptions $modelOptions */
//$this->title = $modelOptions->title;
$_params['_type'] = 'index';
$this->beginContent('@kukusa/views/model-repo/_base.php', $_params);
\Kukusa\Widgets\BoxCard::begin([
    'type' => 'primary',
    'title' => Html::a('ada', $model::modelRepoUrl('create'))
]);
$list = DataLists::begin([
//    'model' => $model,
    'repoModule' => $repoModule,
    'repoModel' => $repoModel,
    'gridView' => $gridView,
    'dataProvider' => $dataProvider
]);

DataLists::end();
\Kukusa\Widgets\BoxCard::end();
//echo \Kukusa\Widgets\Modal::buttonToModal('ada');
$this->endContent();
