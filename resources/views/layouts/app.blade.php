<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="bg-gray-900 text-gray-100 font-sans antialiased min-h-screen">

<aside></aside>

<header></header>

<main class="flex-1 overflow-y-auto p-6">
    {{ $slot }}
</main>

<footer></footer>

@livewireScripts
</body>
</html>
