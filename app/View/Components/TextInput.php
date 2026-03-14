<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextInput extends Component
{
    public $id;

    public $name;

    public $type;

    public $placeholder;

    public $disabled;

    public $value;

    public $extraLabelClass;

    public $required;

    public $extraClass;

    public $icon;

    public $label;

    public $group;

    public function __construct($name, $id, $type, $placeholder = '', $disabled = false, $value = '', $extraLabelClass = '', $required = false, $extraClass = '', $icon = '', $group = false, $label = '')
    {
        $this->label = $label;
        $this->icon = $icon;
        $this->name = $name;
        $this->type = $type;
        $this->group = $group;
        $this->placeholder = $placeholder;
        $this->disabled = $disabled;
        $this->value = $value;
        $this->extraLabelClass = $extraLabelClass;
        $this->required = $required;
        $this->id = $id;
        $this->extraClass = $extraClass;
    }

    public function render(): View|Closure|string
    {
        return view('components.text-input');
    }
}
