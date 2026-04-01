<?php
$pathName = Route::current()->getName();

$selectedColor = "bg-primary-900";
$hoverColor = "hover:bg-slate-800";

$dashboardCss = $requestsCss = $settingsCss = $hoverColor;

match ($pathName) {
    'student.dashboard' => $dashboardCss = $selectedColor,
    'student.requests' => $requestsCss = $selectedColor,
    'settings' => $settingsCss = $selectedColor,
    default => null
};
?>

<aside class="w-64 bg-surface-900 text-slate-300 flex-col hidden md:flex transition-all duration-300">
    <div class="h-16 flex items-center px-6 border-b border-slate-700">
        <span class="text-xl font-bold text-white tracking-wide">Lab<span class="text-accent">Sync</span></span>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-2">
        <a href="{{ route('student.dashboard') }}"
           class="block px-4 py-2 text-white rounded-md font-medium {{$dashboardCss}}" wire:navigate>Lab Equipment</a>
        <a href="{{route('student.requests')}}"
           class="block px-4 py-2 hover:bg-slate-800 hover:text-white rounded-md transition-colors {{$requestsCss}}"
           wire:navigate>My Requests & History</a>
        <a href="{{route('settings')}}"
           class="block px-4 py-2 hover:bg-slate-800 hover:text-white rounded-md transition-colors {{$settingsCss}}"
           wire:navigate>Settings</a>
    </nav>
</aside>
