<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Modules\System\Controllers\rest;


use Kukusa;
use Kukusa\Base\Model;
use Kukusa\ModelRepo\Rest\ActiveController;
use yii\base\InvalidConfigException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\Serializer;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class ApiController
 * @package Kukusa\Controllers
 */
class DefaultController extends ActiveController
{
    public function init()
    {
        parent::init();
    }

//
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                Kukusa\Filters\Auth\RestHttpAuth::class
            ]
        ];
        return $behaviors;
    }
//    public function init()
//    {
//        $model = Kukusa::$app->request->get('model');
//        if(!$model || !Kukusa::$app->dataModel->exist($model))
//            throw new NotFoundHttpException(Kukusa::t('yii', 'Page not found.'));
//        $this->modelClass = Kukusa::$app->dataModel->getClassName($model);
//        $this->serializer = [
//            'class' => Serializer::class,
////            'collectionEnvelope' => '_items'
//        ];
//        parent::init();
//        $this->modelClass::init_controller_rest($this);
////        Kukusa::$app->request['formatters'] =
////            [
////                \yii\web\Response::FORMAT_JSON => [
////                        'class' => 'yii\web\JsonResponseFormatter',
////               'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
////                ]
////            ];
//    }
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function actionPublic()
    {

    }

    public function actionLogin()
    {
        echo 'asa';
        
    }

    public function actionModelConfig()
    {
        if(is_string($this->modelClass))
            $model = new $this->modelClass();
        elseif($this->modelClass instanceof \Kukusa\Base\BaseModelController)
            $model = $this->modelClass;
        if(!isset($model))
            throw new NotFoundHttpException(Kukusa::t('yii', 'Page not found.'));
        $model->loadDefaultValues();
//        $model->scenario = 'updatqe';
//        print_r($model->scenarios());exit;
//        $a = '{"create":{"fields":[{"name":"nama","type":"text","attribute":[]},{"name":"data","type":"text","attribute":[]}],"rules":{"nama":{"required":{"message":"Nama tidak boleh kosong."},"range":{"range":["1","2"],"not":false,"message":"Nama tidak valid.","skipOnEmpty":1},"defaultvalue":{"_value":"\\asa"},"exist":[]},"data":{"required":{"message":"Data tidak boleh kosong."},"range":{"range":["1","2"],"not":false,"message":"Data tidak valid.","skipOnEmpty":1}},"namaa":{"defaultvalue":{"_value":"\\asa"}}}},"update":{"fields":[{"name":"nama","type":"text","attribute":[]},{"name":"data","type":"text","attribute":[]}],"rules":{"nama":{"required":{"message":"Nama tidak boleh kosong."},"range":{"range":["1","2"],"not":false,"message":"Nama tidak valid.","skipOnEmpty":1},"exist":[]},"data":{"required":{"message":"Data tidak boleh kosong."},"range":{"range":["1","2"],"not":false,"message":"Data tidak valid.","skipOnEmpty":1}}}}}';
//echo json_encode($model->getClientConfig(),JSON_UNESCAPED_SLASHES);
//        print_r(json_decode($a, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
//            exit;
//        return ['data'=>$model->getClientConfig(),'callback'=>'a'];
//        print_r($model->getClientConfig());
//        print_r(json_encode($model->getClientConfig(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $model->getClientConfig();
//        print_r(Kukusa::$app->get('validator'));
        foreach($model->attributes() as $attribute){
            if($model->isAttributeActive($attribute)){
                foreach($model->getActiveValidators($attribute) as $validator){
                    print_r($validator->getClientOptions($model, $attribute));
//                    echo $validator->clientValidateAttribute($model, $attribute, null);
                };
//                print_r($validator);
            }
//            print_r($validator->get);
        }
        foreach($model->activeValidators as $i => $validator){
//            print_r($validator);
//            $js = $validator->getClientOptions($model)
        }
    }
    public function actionCustomAction($action = '', $args ='')
    {

        $actionMethod = 'action'.ucfirst($action);
        if(method_exists($this->modelClass, $actionMethod))
            return call_user_func_array([$this->modelClass, $actionMethod], explode('/', $args));
        try{
            return $this->runAction($action, explode('/', $args));
        }catch (\Exception $e) {
            throw new NotFoundHttpException(Kukusa::t('yii', 'Page not found.'));
        }
    }

}