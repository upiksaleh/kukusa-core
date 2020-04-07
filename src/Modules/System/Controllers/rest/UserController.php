<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Controllers\rest;


use Kukusa;
use Kukusa\JWT\JwtHttpBearerAuth;
use Kukusa\Rest\Controller;
use yii\base\InvalidRouteException;
use yii\base\InvalidValueException;

class UserController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => Kukusa\Filters\Auth\RestHttpAuth::class,
            'optional' => [
                'login', 'index'
            ],
        ];
        return $behaviors;
    }

    public function actionIndex()
    {
        Kukusa::$app->request->enableCookieValidation = false;
        print_r($_COOKIE);
        print_r(Kukusa::$app->request->getCookies()->toArray());
        exit;
        return ['ada'];
    }

    public function actionLogin()
    {
        $post = Kukusa::$app->request->getBodyParams();
        $form = new Kukusa\Models\LoginForm();
        $authMethod = Kukusa::$app->request->getHeaders()->get(Kukusa\Filters\Auth\RestHttpAuth::$headerAuthMethod);
        if (!$authMethod)
            throw new InvalidValueException('"authMethod" value tidak ada.');
        $form->load(['f' => $post], 'f');
        $form->login();
        if ($form->hasErrors())
            return $this->errorMessage($form->errors);
        else {
            $userModel = Kukusa::$app->user->identity->model;
            Kukusa::$app->response->getHeaders()->add(Kukusa\Filters\Auth\RestHttpAuth::$headerAuthPayload, Kukusa\Filters\Auth\RestHttpAuth::generateToken($authMethod));
            return [
                'id' => $userModel->id,
                'username' => $userModel->username,
                'fullName' => $userModel->data->fullName
            ];
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function verbs()
    {
        return [
            'index' => ['POST', 'GET', 'OPTIONS'],
            'login' => ['POST', 'GET', 'OPTIONS'],
            'logina' => ['POST', 'GET', 'OPTIONS'],
        ];
    }
}