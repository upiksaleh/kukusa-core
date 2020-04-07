<?php
/**
 *  Yihai
 *
 *  Copyright (c) 2019, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Extensions\Select2;

use Kukusa\Base\ModelOptions;
use Kukusa\Helpers\Html;
use Kukusa\Widgets\InputWidget;
use yii\helpers\ArrayHelper;

class Select2 extends InputWidget
{

    public $items = [];
    /** @var ModelOptions */
    public $modelOptions;

    // EVENTS
    /** @var string  function(e){...} */
    public $onSelect;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!isset($this->clientOptions['multiple']))
            $this->clientOptions['placeholder'] = '-----';
        $this->initPlaceholder();
    }

    /**
     * Select2 plugin placeholder check and initialization
     */
    protected function initPlaceholder()
    {
        $multipleSelection = ArrayHelper::getValue($this->options, 'multiple');
        if (!empty($this->options['prompt']) && empty($this->clientOptions['placeholder'])) {
            $this->clientOptions['placeholder'] = $multipleSelection
                ? ArrayHelper::remove($this->options, 'prompt')
                : $this->options['prompt'];
            return null;
        } elseif (!empty($this->options['placeholder'])) {
            $this->clientOptions['placeholder'] = ArrayHelper::remove($this->options, 'placeholder');
        }
        if (!empty($this->clientOptions['placeholder']) && !$multipleSelection) {
            $this->options['prompt'] = is_string($this->clientOptions['placeholder'])
                ? $this->clientOptions['placeholder']
                : ArrayHelper::getValue((array)$this->clientOptions['placeholder'], 'placeholder', '');
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            if (isset($this->model->{$this->attribute}) && $this->model->{$this->attribute}) {
                $this->value = $this->model->{$this->attribute};
            }
            echo Html::activeDropDownList($this->model, $this->attribute, $this->items, $this->options);
        } else {
            echo Html::dropDownList($this->name, $this->value, $this->items, $this->options);
        }

        if ($this->onSelect)
            $this->clientEvents['select2:select'] = $this->onSelect;

        $this->registerAsset(Select2Asset::class);
        $this->registerJquery('select2');
//        $this->getView()->registerJs('$.fn.modal.Constructor.prototype.enforceFocus = function() {};');
    }
}