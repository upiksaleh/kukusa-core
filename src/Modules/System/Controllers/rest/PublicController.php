<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Controllers\rest;


use Kukusa\Rest\Controller;
use yii\i18n\MessageSource;

class PublicController extends Controller
{
    public function actionLanguage($lang = 'en')
    {
        $language = [];
        if ($lang === 'en')
            $yii_lang = array_flip(\Kukusa::$app->i18n->getAllMessages('yii', 'id'));
        else
            $yii_lang = \Kukusa::$app->i18n->getAllMessages('yii', $lang);
        foreach (\Kukusa::$app->i18n->translations as $category => $translation) {
            if ($category === 'yii') continue;
            $language[$category] = \Kukusa::$app->i18n->getAllMessages($category, $lang);
            if ($category === 'kukusa' || $category === 'kukusa*')
                $language[$category] = array_merge($yii_lang, $language[$category]);
        }
//        print_r($language);exit;
        return $language;
        print_r(\Kukusa::$app->i18n->getMessageSource('yii'));
        echo \Kukusa::$app->i18n->translate('yii', 'View', [], 'id');
        exit;
//                print_r(\Kukusa::$app->i18n->getMessageSource('yii'));exit;
        foreach (\Kukusa::$app->i18n->translations as $translation) {
            /** @var MessageSource $translation */
            $translation = \Kukusa::createObject($translation);
            print_r($translation->translateMessage('yii', 'view', 'en'));

        };
//        print_r(\Kukusa::$app->i18n->getAllMessages());exit;
    }
}