<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */
/** @var \Kukusa\Web\View $this */

$this->beginContent('@kukusa/views/_layouts/_main.php');

use Kukusa\Helpers\Html;
use Kukusa\Helpers\Url;
use Kukusa\Widgets\Alert;

$appAsset = Kukusa::registerAppAsset($this);
?>
    <div class="wrapper">

        <header class="main-header">
            <a href="<?= Url::to(['/system/index']) ?>" class="logo">
                <span class="logo-mini"><?= Html::icon('home') ?></span>
                <span class="logo-lg"><?= Kukusa::$app->id ?></span>
            </a>

            <nav class="navbar navbar-static-top">
                <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>

            </nav>
        </header>
        <aside class="main-sidebar">
            <div class="site-info">
                <div class="logo"><img src="<?= $appAsset->getLogoUrl() ?>" style="" class=""
                                       alt="Logo">
                </div>
                <div class="name"><?= Kukusa::$app->name ?></div>
            </div>
            <section class="sidebar">

                <?php
                if ($backendMenu = Kukusa::$app->webMenu->children('backend')) {
                    echo Kukusa\Widgets\SidebarMenu::widget([
                        'items' => $backendMenu
                    ]);
                }
                ?>
            </section>
        </aside>
        <div class="content-wrapper">

            <section class="content-header">
                <h1>
                    <?php

//                    if (isset($this->params['hints'])) {
//                        echo \yihai\core\theming\Button::widget([
//                            'encodeLabel' => false,
//                            'label' => Html::icon('info'),
//                            'size' => \yihai\core\theming\Button::SIZE_XS,
//                            'type' => 'info',
//                            'clientEvents' => [
//                                'click' => 'function(){
//                                    $("#main-hint-info").toggle();
//                                }'
//                            ]
//                        ]);
//                    } ?>
                    <?= $this->title ?>
                    <?php
                    $helpItems = isset(Kukusa::$app->params['helpItems']) ? Kukusa::$app->params['helpItems'] : '';
                    $helpItem = (isset($helpItems[$this->context->id]) ? $helpItems[$this->context->id] : null);
                    $infoIcon = '<small>' . Html::icon('info-circle') . '</small>';
                    if ($helpItem) {
                        echo Html::a($infoIcon, '', ['title' => 'Bantuan', 'class' => 'chelp-link']);
                    }
                    ?>
                    <small><?= isset($this->params['titleDesc']) ? $this->params['titleDesc'] : '' ?></small>
                </h1>
                <?= \Kukusa\Widgets\Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => 'System',
                        'url' => ['/system']
                    ],
                    'tag' => 'ol',
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </section>
            <section class="content-header crud-hint">
                <?php

//                if (isset($this->params['hints'])) {
//                    $hint = Html::ul($this->params['hints']);
//                    echo Html::beginTag('div', ['id' => 'main-hint-info', 'style' => 'display:none']);
//                    echo \yihai\core\theming\Alert::widget([
//                        'type' => 'info',
//                        'title' => Kukusa::t('yihai', 'Petunjuk / Info'),
//                        'icon' => Html::icon('info', ['class' => 'icon']),
//                        'closeButton' => false,
//                        'body' => $hint
//                    ]);
//                    echo Html::endTag('div');
//                }
                ?>
            </section>
            <section class="content">
                <?php
                Alert::fromFlash(Alert::KEY_CRUD);
                ?>
                <?= $content ?>
            </section>
        </div>
        <footer class="main-footer">
        </footer>
<!--        <aside class="control-sidebar control-sidebar-light">-->
<!--            <ul class="nav nav-tabs nav-justified control-sidebar-tabs">-->
<!--                <li class="active"><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fal fa-cog"></i></a></li>-->
<!--            </ul>-->
<!--            <div class="tab-content">-->
<!--                <div class="tab-pane active" id="control-sidebar-settings-tab">-->
<!--                    --><?//=Html::beginForm('','POST',['id'=>'control-sidebar-settings-form']);?>
<!--                    --><?//=Html::hiddenInput('___settings', 1);?>
<!--                    --><?//=Html::hiddenInput('skin', $skin, ['id'=>'settings-form-skin-id']);?>
<!--                    <div class="form-group">-->
<!--                        <label for="switch-language" class="control-sidebar-subheading">-->
<!--                            --><?//=Kukusa::t('yihai','Bahasa')?>
<!--                        </label>-->
<!--                        <select id="switch-language" name="language" class="form-control">-->
<!--                            --><?php //foreach (Kukusa::$app->params['languageList'] as $key => $v){
//                                echo "<option ".(Kukusa::$app->language === $key ? 'selected':'')." value=\"{$key}\">{$v}</option>";
//                            }
//                            ?>
<!--                        </select>-->
<!--                    </div>-->
<!--                    <div class="form-group" id="skins-list"></div>-->
<!--                    <button class="btn btn-primary btn-block">--><?//=Kukusa::t('yihai','Simpan')?><!--</button>-->
<!--                    --><?//=Html::endForm();?>
<!--                </div>-->
<!--            </div>-->
<!--        </aside>-->
    </div>
<?php
$this->endContent();