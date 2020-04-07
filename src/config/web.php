<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


use Kukusa\Controllers\ModelRepoController;

return [
    'id' => 'kukusa',
    'name' => 'Kukusa App',
    'controllerNamespace' => 'App\Controllers',
    'defaultRoute' => 'system',
    'controllerMap' => [
        'model-repo' => [
            'class' => ModelRepoController::class,
            'viewPath' => '@kukusa/views/model-repo'
        ]
    ],
    'components' => [
        'user' => [
            'class' => "Kukusa\Web\User",
            'groups' => [
                'system' => 'Kukusa\Models\SysUsersSystem'
            ],
            'identityClass' => 'Kukusa\Web\UserIdent',
            'enableAutoLogin' => true,
            'loginUrl' => ['system/login']
        ],
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'webMenu' => [
            'class' => 'Kukusa\Web\WebMenu'
        ],
        'jwt' => [
            'class' => 'Kukusa\JWT\Jwt',
        ],
        'view' => [
            'class' => 'Kukusa\Web\View',
        ],
        'theme' => [
            'class' => 'Kukusa\Theme\ThemeComponent',
        ],
        'session' => [
//            'class' => \yii\web\Session::class,
            'name' => 'KUKUSASESSID',
        ],
        'i18n' => [
            'class' => 'Kukusa\i18n\I18N'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                '/' => 'system/default/index',
                '<controller:\w+>' => '<controller>/index',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>' => '<module>/<controller>/index',
                [
                    'class' => \Kukusa\web\UrlRule::class,
                    'pattern' => 'rest/default/v1/login',
                    'route' => '/system/default/login-api'
                ],
//                [
//                    'class' => \Kukusa\web\UrlRule::class,
//                    'pattern' => 'ctrl/<module>/<action>',
//                    'route' => '/system/default/<action>'
//                ],
//                [
//                    'class' => \Kukusa\rest\UrlRule::class,
//                    'controller' => ['u'=>'ada'],
//                ],
                'module-repo-v1' => [
                    'class' => \Kukusa\Web\ModelRepoUrlRule::class,
                    'controller' => 'model-repo',
                ],
                'module-repo-api-v1' => [
                    'class' => \Kukusa\Web\ModelRepoRestUrlRule::class,
                    'controller' => 'system/rest/default',
                ],
//                [
//                    'class' => \Kukusa\Web\RestUrlRule::class,
//                    'controller' => ['v1/public' => 'system/rest/public'],
//                    'patterns' => [
//                        '{action}' => '<action>'
//                    ]
//                ],
//                [
//                    'class' => \Kukusa\Web\RestUrlRule::class,
//                    'controller' => ['v1/_user' => 'system/rest/user'],
//                    'patterns' => [
//                        'GET,POST,OPTIONS index' => 'index',
//                        'GET,POST,OPTIONS login' => 'login',
//                        'GET,POST,OPTIONS logina' => 'login',
////                        'POST,GET index' => 'index',
//                    ],
//                    'tokens' => []
//                ],
//                [
//                    'class' => \Kukusa\Web\RestUrlRule::class,
//                    'controller' => ['v1' => 'system/rest/default'],
//                ],
            ],
        ],
    ]
];