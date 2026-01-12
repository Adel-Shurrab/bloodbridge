<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Layout extends Component
{
    /**
     * The title of the page
     */
    public string $title;

    /**
     * The description of the page
     */
    public ?string $description;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $title = null, ?string $description = null)
    {
        $this->title = $title ?? app(\App\Settings\GeneralSettings::class)->site_name;
        $this->description = $description;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.public');
    }
}
