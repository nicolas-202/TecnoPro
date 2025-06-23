<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TecnoPro Soluciones</title>
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">  
    <link href="{{ asset('css/empleados.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex flex-col min-h-screen">
        <!-- Header: Title and Authentication Options -->
        <header class="bg-blue-700 text-white p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">TECNOPRO SOLUCIONES</h1>
            <nav class="space-x-4">
                @auth
                    @php
                        $user = Auth::user()->load('empleado');
                    @endphp
                    <a href="{{ route('cuenta.perfil') }}" class="hover:underline">Usuario</a>
                    <a href="{{ route('carrito.index') }}" class="hover:underline">Carrito de Compras</a>
                    <a href="{{ route('logout') }}" class="hover:underline" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar Sesión</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:underline">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="hover:underline">Registrarse</a>
                @endauth
            </nav>
        </header>

        <!-- Navigation Bar: Main Sections -->
        <nav class="bg-gray-500 p-4 shadow-md">
            <div class="flex space-x-6 overflow-x-auto">
                <a href="{{ route('index') }}" class="hover:text-blue-700 {{ request()->routeIs('index') ? 'text-blue-700 font-bold' : '' }}">Inicio</a>
                <a href="{{ route('catalogo.index') }}" class="hover:text-blue-700 {{ request()->routeIs('catalogo.index') ? 'text-blue-700 font-bold' : '' }}">Catálogo</a>
                <a href="{{ route('support') }}" class="hover:text-blue-700 {{ request()->routeIs('support') ? 'text-blue-700 font-bold' : '' }}">Atención al Cliente</a>
                @auth
                    @if(auth()->user()->empleado && auth()->user()->empleado->estadoEmpleado == 1)
                        <a href="{{ route('kardex.index') }}" class="hover:text-blue-700 {{ request()->routeIs('kardex.index') ? 'text-blue-700 font-bold' : '' }}">Inventario</a>
                        <a href="{{ route('config') }}" class="hover:text-blue-700 {{ request()->routeIs('config') ? 'text-blue-700 font-bold' : '' }}">Configuración</a>
                    @endif
                @endauth
            </div>
        </nav>

        <!-- Main Content and Footer -->
        <div class="flex flex-1">
            <div class="w-full p-6 bg-white shadow-lg flex-1">
                <main class="min-h-[calc(100vh-12rem)]">
                    @yield('content')
                </main>
                <footer class="mt-6 text-center text-gray-600">
                    <p>Contacto | Copyright ©2025</p>
                </footer>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/layout.js') }}"></script>
</body>
</html>