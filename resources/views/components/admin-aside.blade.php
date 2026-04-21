<?php

use App\Models\AdminRoutesEnum;

$selectedColor = "bg-primary-900 text-white";
$hoverColor = "hover:bg-slate-800 hover:text-white";

$dashboardCss = $equipmentCss = $requestsCss = $settingsCss = $hoverColor;

match ($route) {
    AdminRoutesEnum::DASHBOARD => $dashboardCss = $selectedColor,
    AdminRoutesEnum::EQUIPMENT => $equipmentCss = $selectedColor,
    AdminRoutesEnum::REQUESTS => $requestsCss = $selectedColor,
    AdminRoutesEnum::SETTINGS => $settingsCss = $selectedColor,
    default => error_log("Unsupported admin route passed to `admin-aside`. Route: " . $route)
}
?>

<aside class="w-64 bg-surface-900 text-slate-300 flex-col hidden md:flex transition-all duration-300">
    <div class="h-16 flex items-center px-6 border-b border-slate-700">
        <span class="text-xl font-bold text-white tracking-wide">Lab<span class="text-accent">Sync</span></span>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-2">
        <a href="{{ route('admin.dashboard') }}"
           class="block px-4 py-2 rounded-md font-medium {{$dashboardCss}}" wire:navigate>Dashboard</a>
        <a href="{{ route('admin.equipment') }}"
           class="block px-4 py-2 rounded-md transition-colors {{$equipmentCss}}"
           wire:navigate>Equipment</a>
        <a href="{{route('admin.requests')}}"
           class="block px-4 py-2 rounded-md transition-colors {{$requestsCss}}">Requests</a>
        <a href="{{route('settings')}}"
           class="block px-4 py-2 rounded-md transition-colors {{$settingsCss}}">Settings</a>
    </nav>
</aside>
