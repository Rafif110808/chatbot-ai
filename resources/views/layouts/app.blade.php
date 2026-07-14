<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
          dark: localStorage.getItem('dark') === 'true'
      }"
      x-init="document.documentElement.classList.toggle('dark', dark);
              $watch('dark', val => {
                  localStorage.setItem('dark', val);
                  document.documentElement.classList.toggle('dark', val);
              })"
      :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="h-screen overflow-hidden bg-amber-50 dark:bg-stone-950 transition-colors">
            @include('layouts.navigation')

            <main class="h-[calc(100vh-4rem)]">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>