<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EmailLayout extends Component
{
    /**
     * Create a new EmailLayout component instance.
     *
     * @param  string       $title      Used as the <title> tag and email subject hint.
     * @param  string|null  $preheader  Short preview text shown in inbox before opening.
     *                                  Padded with invisible characters so body copy
     *                                  does not bleed into the preview snippet.
     */
    public function __construct(
        public string $title = '',
        public ?string $preheader = null,
    ) {
        if (empty($this->title)) {
            $this->title = config('app.name', 'Notification');
        }
    }

    public function render(): View|Closure|string
    {
        return view('layouts.email');
    }
}
