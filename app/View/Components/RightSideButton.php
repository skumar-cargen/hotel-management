<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RightSideButton extends Component
{
    public $link;

    public $title;

    /**
     * Create a new component instance.
     */
    public function __construct($link, $title)
    {
        $this->link = $link;
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.right-side-button');
    }
}
