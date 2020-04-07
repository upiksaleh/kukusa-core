<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Models\Form;


use Kukusa;
use Kukusa\Base\Model;
use Kukusa\Web\UserIdent;

class LoginForm extends Model implements Kukusa\Base\LoginFormInterface
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = null;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
//            ['group', 'in', 'range' => array_keys(Kukusa::$app->user->groups)],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function attributeLabels()
    {
        return [
            'username' => Kukusa::t('kukusa', 'Nama pengguna/Email'),
            'password' => Kukusa::t('kukusa', 'Kata sandi'),
            'rememberMe' => Kukusa::t('kukusa', 'Ingatkan saya'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $this->_user = $this->getUser();

            if (!$this->_user || !$this->_user->validatePassword($this->password)) {
                $this->addError($attribute, Kukusa::t('kukusa', 'Nama pengguna atau kata sandi salah.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Kukusa::$app->user->login($this->_user, $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return UserIdent|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = UserIdent::findByUsernameOrEmail($this->username);
        }

        return $this->_user;
    }
}