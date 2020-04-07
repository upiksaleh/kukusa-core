<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Modules\System\Controllers;


use Kukusa;
use Kukusa\Web\Controller;

class DefaultController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        Kukusa::$app->setHomeUrl('index');
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'only' => ['logout', 'index', 'profile', 'profile-update','change-password'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return Kukusa::$app->response->redirect(['/system/login']);
                },
            ],
        ];
    }
    public function actions()
    {
        return [
            'login' => 'Kukusa\Actions\LoginAction',
            'logout' => 'yihai\core\actions\LogoutAction',
            'profile' => 'yihai\core\actions\ProfileAction',
            'profile-update' => 'yihai\core\actions\ProfileUpdateAction',
            'change-password' => 'yihai\core\actions\ChangePasswordAction',
        ];
    }

    public function actionIndex()
    {
        $dashboardWidgets = [];
        foreach(Kukusa::$app->modules as $name => $config){
            if($config instanceof Kukusa\Base\Module && $config->dashboardWidgetClass){
                $dashboardWidgets[$name] = $config->dashboardWidgetClass;
            }
        }
        return $this->render('index', [
            'dashboardWidgets' => $dashboardWidgets
        ]);
    }

}