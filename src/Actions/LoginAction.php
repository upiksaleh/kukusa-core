<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Actions;


use Kukusa;
use Kukusa\Base\Action;
use yii\base\InvalidConfigException;

class LoginAction extends Action
{
    /**
     * class login form. jika kosong berarti menggunakan class \yihai\core\models\form\LoginForm
     * @var array|string
     */
    public $loginFormClass = 'Kukusa\Models\Form\LoginForm';

    public $layout = '@kukusa/views/_layouts/blank-with-theme';
    /**
     * @var string view file
     */
    public $view = '@kukusa/views/_pages/login';

    /**
     * fungsi yang akan dipanggil ketika login berhasil. default redirect ke url sebelumnya
     * @var callable
     */
    public $on_login;

    /**
     * text pada header
     * @var string
     */
    public $header_text;

    /**
     * menampilkan checkbox remember me
     * @var bool
     */
    public $show_remember_checkbox = true;

    /**
     * placeholder untuk inputan user
     * @var string
     */
    public $placeholder_user;
    /**
     * placeholder untuk inputan password
     * @var string
     */
    public $placeholder_pass;
    /**
     * jika true, maka select group akan ditampilkan
     * @var bool
     */
    public $show_group = true;

    /**
     * menampilkan label pada input
     * @var bool
     */
    public $show_label_input = false;

    public function init()
    {
        parent::init();
        $this->controller->layout = $this->layout;
        if (!$this->on_login)
            $this->on_login = function () {
                return $this->controller->goBack();
            };
        if (!$this->header_text)
            $this->header_text = Kukusa::$app->name;
        if (!$this->placeholder_user)
            $this->placeholder_user = Kukusa::t('kukusa', 'Nama pengguna/Email');
        if (!$this->placeholder_pass)
            $this->placeholder_pass = Kukusa::t('kukusa', 'Kata sandi');
    }

    /**
     * @return string|\yii\web\Response
     * @throws InvalidConfigException
     */
    public function run($group = '')
    {
        // jika bukan guest atau telah menjadi user
        if (!Kukusa::$app->user->isGuest)
            return $this->controller->goHome();     // redirect ke home
        $modelForm = Kukusa::createObject($this->loginFormClass);
//        if ($this->loginFormClass)
//            $modelForm = new $this->loginFormClass();
//        else
//            $modelForm = new LoginForm();
        if (!$modelForm instanceof Kukusa\Base\LoginFormInterface) {
            throw new InvalidConfigException(Kukusa::t('kukusa', '"{class}" harus instance dari {classRequired}', ['class' => 'loginFormClass', 'classRequired' => 'Kukusa\Base\LoginFormInterface']));
        }
        if ($modelForm->load(Kukusa::$app->request->post()) && $modelForm->login()) {
            return call_user_func($this->on_login);
        }
        $modelForm->password = '';
        // render login page
        return $this->controller->render($this->view, [
            'model' => $modelForm,
            'group' => $group,
            'header_text' => $this->header_text,
            'show_remember_checkbox' => $this->show_remember_checkbox,
            'placeholder_user' => $this->placeholder_user,
            'placeholder_pass' => $this->placeholder_pass,
            'show_group' => $this->show_group,
            'show_label_input' => $this->show_label_input,
        ]);
    }
}