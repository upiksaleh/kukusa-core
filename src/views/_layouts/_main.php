<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

/** @var \Kukusa\Web\View $this */

use Kukusa\Helpers\Html;

if ($this->params('_layouts.useMainAsset', true)) {
    \Kukusa\Assets\MainAsset::register($this);
}
?>
<?php $this->beginPage() ?>
<!DOCTYPE HTML>
<html lang="<?= Kukusa::$app->language ?>">
<head>
    <meta charset="<?= Kukusa::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<?php $this->beginBody() ?>
<?php
echo $content
?>
<?php $this->endBody() ?>
<script>
    $('.content a[title], .content button[title]').tooltip({trigger: 'hover'});
    jQuery(document).on("pjax:success", function (event) {
        $('.content a[title], .content button[title]').tooltip({trigger: 'hover'});
    });
</script>
</body>
</html>
<?php $this->endPage() ?>
