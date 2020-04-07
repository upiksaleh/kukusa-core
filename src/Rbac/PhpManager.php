<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Rbac;


use yii\helpers\FileHelper;

class PhpManager extends \yii\rbac\PhpManager
{

    public $itemFile = '@app/storages/kukusa/rbac/items.php';

    public $assignmentFile = '@app/storages/kukusa/rbac/assignments.php';

    public $ruleFile = '@app/storages/kukusa/rbac/rules.php';

}