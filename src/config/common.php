<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */


return [
    'language' => 'id',
    'bootstrap' => ['Kukusa\\Bootstrap', 'log'],
    'components' => [
        'authManager' => [
            'class' => 'Kukusa\Rbac\DbManager',
        ],
        'i18n' => [
            'translations' => [
                'kukusa*' => [
                    'class' => 'Kukusa\i18n\PhpMessageSource',
                    'basePath' => '@kukusa/messages',
                    'pathMap' => [
                        '@kukusa/messages'
                    ],
                    'fileMap' => [
                    ]
                ],
            ]
        ],
        'modelRepo' => [
            'class' => \Kukusa\Base\ModelRepo::class,
            'models' => [
            ]
        ],
    ],
    'modules' => [
        'system' => [
            'class' => 'Kukusa\Modules\System\Module',
        ]
    ]
];