<?php namespace Backend\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Lang;
use ApplicationException;

/**
 * Color picker
 * Renders a color picker field.
 *
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class ColorPicker extends FormWidgetBase
{
    //
    // Configurable properties
    //

    /**
     * @var array Default available colors
     */
    public $availableColors = [
        '#1abc9c', '#16a085',
        '#2ecc71', '#27ae60',
        '#3498db', '#2980b9',
        '#9b59b6', '#8e44ad',
        '#34495e', '#2b3e50',
        '#f1c40f', '#f39c12',
        '#e67e22', '#d35400',
        '#e74c3c', '#c0392b',
        '#ecf0f1', '#bdc3c7',
        '#95a5a6', '#7f8c8d',
    ];

    /**
     * @var bool Allow empty value
     */
    public $allowEmpty = false;

    /**
     * @var bool Show opacity slider
     */
    public $showAlpha = false;

    //
    // Object properties
    //

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'colorpicker';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'availableColors',
            'allowEmpty',
            'showAlpha',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('colorpicker');
    }

    /**
     * Prepares the list data
     */
    public function prepareVars()
    {
        $this->vars['name'] = $this->getFieldName();
        $this->vars['value'] = $value = $this->getLoadValue();
        $this->vars['availableColors'] = $this->getAvailableColors();
        $this->vars['allowEmpty'] = $this->allowEmpty;
        $this->vars['showAlpha'] = $this->showAlpha;
        $this->vars['isCustomColor'] = !in_array($value, $this->vars['availableColors']);

    }

    /**
     * Gets the appropriate list of colors.
     *
     * @return array
     */
    protected function getAvailableColors()
    {
        if (is_array($this->availableColors)) {
            return $this->availableColors;
        }
        elseif (is_string($this->availableColors) && !empty($this->availableColors)) {
            if (!$this->model->methodExists($this->availableColors)) {
                throw new ApplicationException(Lang::get('backend::lang.field.colors_method_not_exists', [
                    'model'  => get_class($this->model),
                    'method' => $this->availableColors,
                    'field'  => $this->formField->fieldName
                ]));
            }
            return $this->model->{$this->availableColors}(
                $this->formField->fieldName,
                $this->formField->value,
                $this->formField->config
            );
        }
    }

    /**
     * @inheritDoc
     */
    protected function loadAssets()
    {
        $this->addCss('vendor/spectrum/spectrum.css', 'core');
        $this->addJs('vendor/spectrum/spectrum.js', 'core');
        $this->addCss('css/colorpicker.css', 'core');
        $this->addJs('js/colorpicker.js', 'core');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return strlen($value) ? $value : null;
    }
}
