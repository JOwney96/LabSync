<?php

namespace App\View\Components;

use App\Models\AdminRoutesEnum;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AdminAside extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public AdminRoutesEnum $route = AdminRoutesEnum::DASHBOARD,
    )
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin-aside');
    }
}
