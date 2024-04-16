<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('Beginopdracht')</title>

    {{-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/> --}}
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    @livewireStyles
</head>

<body>
    @livewire('navigation-component')

    <main class="bg-fixed bg-cover bg-white min-h-screen  relative">
        <div class="z-10 relative">
            {{ $slot }}
        </div>
    </main>
    <footer class="relative">
        <div class="flex text-white">
            <div class="bg-gray-800  p-10 pt-20 pb-20 w-1/4">
                <div class="w-20">
                    <img src="img/nnneonwhite.svg" alt="Logo">
                </div>
            </div>
            <div class="bg-gray-800  p-10 pt-20 pb-20 w-1/4">
                <div class="">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore et dolore magna aliqua. Eu consequat ac felis donec et odio</div>
            </div>
            <div class="bg-gray-800  p-10 pt-20 pb-20 w-1/4">
                <div class="">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt
                    <ul>
                        <li>-Item 1</li>
                        <li>-Item 2</li>
                        <li>-Item 3</li>
                    </ul>
                </div>
            </div>
            <div class="bg-gray-800  p-10 pt-20 pb-20 w-1/4">
                <div class="">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                    incididunt ut labore et dolore magna aliqua. Eu consequat ac felis donec et odio</div>
            </div>
        </div>
        <div class="bg-purple-600 pl-10">
            <p>© 2024 Daniiiiii he</p>
        </div>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script> --}}
    @livewireScripts
    @livewireStyles
</body>

</html>
