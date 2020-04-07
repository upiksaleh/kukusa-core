<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\ModelRepo\Actions;


use Kukusa;
use Kukusa\Base\ModelRepo;
use Kukusa\Widgets\Alert;
use yii\web\MethodNotAllowedHttpException;

class DeleteAction extends Action
{
    protected function beforeRun()
    {
        $verb = Kukusa::$app->getRequest()->getMethod();
        $allowed = array_map('strtoupper', ['POST']);
        if (!in_array($verb, $allowed)) {
            // https://tools.ietf.org/html/rfc2616#section-14.7
            Kukusa::$app->getResponse()->getHeaders()->set('Allow', implode(', ', $allowed));
            throw new MethodNotAllowedHttpException('Method Not Allowed. This URL can only handle the following request methods: ' . implode(', ', $allowed) . '.');
        }
        return parent::beforeRun();
    }
    public function run($id){
//        $multiples = Kukusa::$app->request->getBodyParam('multiple');
//        if($multiples) {
//            $deleted = false;
//            $model = $this->model;
//            $primaryKeys = $model::primaryKey();
//            foreach(json_decode($multiples, true) as $multiple){
//                if(is_string($multiple) || is_int($multiple)){
//                    $multiple_array =[];
//                    foreach($primaryKeys as $pk){
//                        $multiple_array[$pk] = $multiple;
//                    }
//                    $multiple = $multiple_array;
//                }
//                if ($this->findModelDelete($multiple)->delete()) {
//                    $deleted = true;
//                }
//            }
//            if($deleted){
//                Alert::addFlashAlert(Alert::KEY_CRUD, 'success', Kukusa::t('yihai', 'Sukses menghapus items'), true);
//            }else{
//                Alert::addFlashAlert(Alert::KEY_CRUD, 'danger', Kukusa::t('yihai', 'Gagal menghapus items'), true);
//            }
//        }else{
            $queryParams = Kukusa::$app->request->getQueryParams();
            $primaryKeys = $this->model::primaryKey();
            $params = [];
            foreach($primaryKeys as $pk){
                if(isset($queryParams[$pk]))
                    $params[$pk] = $queryParams[$pk];
                else
                    $params[$pk] = $id;
            }
            if ($this->findModel($params)->delete()) {
                Alert::addFlashAlert(Alert::KEY_CRUD, Alert::TYPE_SUCCESS, Kukusa::t('kukusa', 'Sukses menghapus item'), true);
            }else{
                Alert::addFlashAlert(Alert::KEY_CRUD, Alert::TYPE_DANGER, Kukusa::t('kukusa', 'Gagal menghapus item'), true);
            }
//        }
        return $this->controller->redirect($this->goBackUrl);
    }
}