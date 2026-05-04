<?php

namespace App\View\Components;

use App\Models\StudentRoutesEnum;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StudentAside extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public StudentRoutesEnum $route = StudentRoutesEnum::DASHBOARD,
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.student-aside');
    }
}
