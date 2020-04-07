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
use Kukusa\Helpers\Html;
use Kukusa\Widgets\Alert;

class FormAction extends Action
{
    public $viewFile = '@kukusa/views/model-repo/form';
    public $type = ModelRepo::FORM_TYPE_CREATE;
    public $messageSuccess = 'Success';
    public $messageError = 'Error';

    public function init()
    {
        parent::init();
        $this->model->init_forms($this->type);
    }

    public function run($id = '')
    {
        if ($this->type === ModelRepo::FORM_TYPE_UPDATE && $id) {
            if($this->modelOptions->useLinkEncoded)
                $id = Kukusa\Helpers\Url::safe_b64decode($id);
            $this->model = $this->model->findModelOne($id);
        }
        if ($this->model->load(Kukusa::$app->request->post())) {
            if ($this->model->validate()) {
                if ($this->model->save(false)) {
                    $msg = ($this->messageSuccess ? $this->messageSuccess : Kukusa::t('yihai', 'Sukses'));
                    Alert::addFlashAlert(Alert::KEY_CRUD, Alert::TYPE_SUCCESS, $msg, true);
                    return $this->controller->redirect($this->goBackUrl);
                }
            } else {
                $msg = ($this->messageError ? $this->messageError : Kukusa::t('yihai', 'Kesalahan'));
                $messageDangers = [Html::tag('b', $msg)];
                foreach ($this->model->getErrors() as $attribute => $err) {
                    $messageDangers[] = implode('<br/>', $err);
                }
                Alert::addFlashAlert(Alert::KEY_CRUD, Alert::TYPE_DANGER, implode('<br/>', $messageDangers));
            }
        }
        return $this->controller->render($this->viewFile, $this->viewParams([
            'type' => $this->type
        ]));
    }

}