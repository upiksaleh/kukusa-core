<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Widgets;


use Kukusa;
use Kukusa\Helpers\Html;

class ModalDelete extends Modal
{
    public $type = 'danger';
    public $size = self::SIZE_SMALL;

    public function init()
    {
        $this->body = Kukusa::t('kukusa', 'Anda yakin ingin menghapus item ini?');
        $this->header = Html::icon('trash') . ' ' . Kukusa::t('kukusa', 'Hapus item?');
        $this->footer = Html::beginForm([''], 'post', ['id' => 'yihai-crud-basemodal-deleteform'])
            . Html::hiddenInput('multiple')
            . Html::submitButton(
                Kukusa::t('kukusa', 'Ya'),
                ['class' => 'btn btn-danger']
            ) . Html::button(Kukusa::t('kukusa', 'Tidak'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal'])
            . Html::endForm();

        $this->clientEvents[self::EVENT_ON_SHOW] = 'function(event){
        var href=$(event.relatedTarget).attr("href");
        var multiple =  $(event.relatedTarget).attr("data-multiple");
        if(multiple){
//            href = href+"?yihai_multiple="+multiple;
        $(this).find("#yihai-crud-basemodal-deleteform").find(\'input[name="multiple"]\').val((multiple));
            $(this).find(".delete-text-info").text("' . Kukusa::t('kukusa', 'Anda yakin ingin menghapus item yang dipilih?') . '");
        }else{
            $(this).find(".delete-text-info").text("' . Kukusa::t('kukusa', 'Anda yakin ingin menghapus item ini?') . '");
        }
        $(this).find("#yihai-crud-basemodal-deleteform").attr("action", href)
    }';
        parent::init();
    }

}