<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Filters\Auth;


use Kukusa;
use yii\base\InvalidConfigException;
use yii\filters\auth\AuthMethod;
use yii\filters\auth\HttpHeaderAuth;
use yii\web\Cookie;

class RestHttpAuth extends AuthMethod
{
    const METHOD_BASIC = 'basic';

    public static $headerAuthMethod = 'X-Auth-Method';

    public static $headerAuthPayload = 'X-Auth-Payload';

    /**
     * {@inheritdoc}
     */
    public function authenticate($user, $request, $response)
    {
        $authMethod = $request->getHeaders()->get(static::$headerAuthMethod);
        $authPayload = $request->getHeaders()->get(static::$headerAuthPayload);
        if ($authMethod !== null && $authPayload !== null) {
            $identity = null;
            if ($authMethod === self::METHOD_BASIC) {
                $authPayload = static::decryptToken($authPayload);
                if (!is_array($authPayload))
                    return null;
                if ($authPayload['validTime'] < time())
                    return null;
                if($authPayload['ua'] !== $request->userAgent)
                    return null;
                if($authPayload['ip'] !== $request->getRemoteIP())
                    return null;
                $identity = $user->loginByAccessToken($authPayload, $authMethod);
            }
            if ($identity)
                $response->getHeaders()->add(static::$headerAuthPayload, static::generateToken($authMethod));
            return $identity;
        }
        return null;
    }

    public static function decryptToken($data, $isArray = true)
    {
        $data = Kukusa::$app->security->decryptByKey(base64_decode($data), Kukusa::$app->request->cookieValidationKey);
        if ($isArray)
            return json_decode($data, true);
        return $data;
    }

    public static function generateToken($authMethod)
    {

        $data = '';
        if ($authMethod === self::METHOD_BASIC) {
            $data = json_encode([
                'id' => Kukusa::$app->user->getId(),
                'ua' => Kukusa::$app->request->userAgent,
                'validTime' => strtotime('+1 day'),
                'ip' => Kukusa::$app->request->getRemoteIP()
            ]);
        }
        if ($data === '') throw new InvalidConfigException('Data token kosong.');
        return base64_encode(Kukusa::$app->security->encryptByKey($data, Kukusa::$app->request->cookieValidationKey));

    }
}