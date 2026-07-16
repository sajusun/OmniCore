<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ErrorLayout extends Component
{
    public function render(): View|Closure|string
    {
        return view('layouts.error');
    }
}
