<?php
/**
 * Kukusa CMS
 *
 * Copyright (c) 2020, CodeUP.
 * @author  Upik Saleh <upik@codeup.id>
 */

namespace Kukusa\Widgets;

use Kukusa\Base\Widget;
use Kukusa;
use Kukusa\Helpers\Html;
use yii\helpers\ArrayHelper;

class Alert extends Widget
{
    const KEY_CRUD = 'crud-msg';
    const TYPE_SUCCESS = 'success';
    const TYPE_DANGER = 'danger';
    const TYPE_ERROR = 'error';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    /**
     * @var string the body content in the alert component. Note that anything between
     * the [[begin()]] and [[end()]] calls of the Alert widget will also be treated
     * as the body content, and will be rendered before this.
     */
    public $body;
    /**
     * @var array|false the options for rendering the close button tag.
     * The close button is displayed in the header of the modal window. Clicking
     * on the button will hide the modal window. If this is false, no close button will be rendered.
     *
     * The following special options are supported:
     *
     * - tag: string, the tag name of the button. Defaults to 'button'.
     * - label: string, the label of the button. Defaults to '&times;'.
     *
     * The rest of the options will be rendered as the HTML attributes of the button tag.
     * Please refer to the [Alert documentation](http://getbootstrap.com/components/#alerts)
     * for the supported HTML attributes.
     */
    public $closeButton = [];

    public $type;
    public $alertTypes = [
        self::TYPE_ERROR => 'alert-danger',
        self::TYPE_DANGER => 'alert-danger',
        self::TYPE_SUCCESS => 'alert-success',
        self::TYPE_INFO => 'alert-info',
        self::TYPE_WARNING => 'alert-warning'
    ];
    public $icon;
    public $title;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        $this->initOptions();

        echo Html::beginTag('div', $this->options) . "\n";
        echo $this->renderBodyBegin() . "\n";
        if ($this->icon || $this->title) {
            echo Html::beginTag('h4');
            if ($this->icon)
                echo $this->icon . ' ';
            if ($this->title)
                echo $this->title;
            echo Html::endTag('h4');
        }
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        echo "\n" . $this->renderBodyEnd();
        echo "\n" . Html::endTag('div');

        $this->registerJquery('alert');
    }

    /**
     * Renders the close button if any before rendering the content.
     * @return string the rendering result
     */
    protected function renderBodyBegin()
    {
        return $this->renderCloseButton();
    }

    /**
     * Renders the alert body (if any).
     * @return string the rendering result
     */
    protected function renderBodyEnd()
    {
        return $this->body . "\n";
    }

    /**
     * Renders the close button.
     * @return string the rendering result
     */
    protected function renderCloseButton()
    {
        if (($closeButton = $this->closeButton) !== false) {
            $tag = ArrayHelper::remove($closeButton, 'tag', 'button');
            $label = ArrayHelper::remove($closeButton, 'label', '&times;');
            if ($tag === 'button' && !isset($closeButton['type'])) {
                $closeButton['type'] = 'button';
            }

            return Html::tag($tag, $label, $closeButton);
        } else {
            return null;
        }
    }

    /**
     * Initializes the widget options.
     * This method sets the default values for various options.
     */
    protected function initOptions()
    {
        Html::addCssClass($this->options, ['alert', 'fade', 'in']);
        if ($this->type && isset($this->alertTypes[$this->type]))
            Html::addCssClass($this->options, [$this->alertTypes[$this->type]]);

        if ($this->closeButton !== false) {
            $this->closeButton = array_merge([
                'data-dismiss' => 'alert',
                'aria-hidden' => 'true',
                'class' => 'close',
            ], $this->closeButton);
        }
    }

    public static function addFlashAlert($key, $type, $message, $autoHide = false)
    {
        Kukusa::$app->session->addFlash($key, [
            'msg' => $message,
            'type' => $type,
            'autoHide' => $autoHide
        ]);
    }

    /**
     * @param $key
     * @param array|static $options
     */
    public static function fromFlash($key, $options = [])
    {
        $session = Kukusa::$app->session;
        $items = $session->getFlash($key, []);

        if (empty($items)) return;

        foreach ($items as $flash) {
            $alert = static::begin(ArrayHelper::merge([
                'options' => ['class' => 'kukusa-alert-' . $key],
                'body' => $flash['msg'],
                'type' => $flash['type'],
            ], $options));

            if ($flash['autoHide']) {
                $alert->getView()->registerJs("$(\".kukusa-alert-{$key}\").fadeTo(2000, 500).slideUp(500, function(){\$(\".kukusa-alert-{$key}\").slideUp(500)});");
            }
            static::end();
        }
    }
}