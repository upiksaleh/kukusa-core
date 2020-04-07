<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Web;


use Kukusa;
use Kukusa\Models\SysUser;
use yii\base\BaseObject;
use yii\web\IdentityInterface;

/**
 * Class UserIdent
 * @package Kukusa\Web
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $group
 * @property SysUser $model
 * @property \Kukusa\Base\BaseUser $data
 */
class UserIdent extends BaseObject implements IdentityInterface
{
    private $_id;

    private $_username;
    private $_email;

    /** @var string grup user */
    private $_group;

    /** @var SysUser */
    private $_model;

    /** @var \Kukusa\Base\BaseUser */
    private $_data;

    // --------------------------------------------------------------------
    public function getUsername()
    {
        return $this->_username;
    }

    public function getGroup()
    {
        return $this->_group;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function setModel($model)
    {
        $this->_model = $model;
    }

    public function setData($dataUser)
    {
        $this->_data = $dataUser;
    }

    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param SysUser $user
     * @return UserIdent
     */
    public static function newI($user)
    {
        if (!$user)
            return NULL;
        return new static(['model' => $user, 'data' => $user->data]);
    }

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->_group = $this->_model->group;
        $this->_id = $this->_model->id;
        $this->_username = $this->_model->username;
        $this->_email = $this->_model->email;
    }

    // --------------------------------------------------------------------

    public static function findByUsername($username)
    {
        return static::newI(SysUser::findOne(['username' => $username, 'status' => SysUser::STATUS_ACTIVE]));
    }

    public static function findByEmail($email)
    {
        return static::newI(SysUser::findOne(['email' => $email, 'status' => SysUser::STATUS_ACTIVE]));
    }

    public static function findByUsernameOrEmail($username)
    {
        return static::newI(SysUser::find()->andWhere([
            'status' => SysUser::STATUS_ACTIVE,
        ])
            ->andWhere(['or',
                ['username' => $username],
                ['email' => $username]
            ])->one()
        );
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Kukusa::$app->security->validatePassword($password, $this->_model->password);
    }

    // --------------------------------------------------------------------

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return static::newI(SysUser::findOne([
            'id' => $id,
            'status' => SysUser::STATUS_ACTIVE
        ]));
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface|null the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if($type === Kukusa\JWT\JwtHttpBearerAuth::class){
            return static::newI(SysUser::findOne([
                'id' => $token->getClaim('uid'),
                'status' => SysUser::STATUS_ACTIVE
            ]));
//            echo $token->getClaim('uid');exit;
        }elseif($type === Kukusa\Filters\Auth\RestHttpAuth::METHOD_BASIC && isset($token['id'])){
            return static::newI(SysUser::findOne([
                'id' => $token['id'],
                'status' => SysUser::STATUS_ACTIVE
            ]));
        }
        return null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->_model->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->_model->auth_key === $authKey;
    }
}