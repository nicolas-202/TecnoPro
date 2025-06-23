@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8 space-y-8">
    <!-- Proceso de Devolución -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 text-center mb-6">Proceso de Devolución y Reembolso</h1>
        <p class="text-gray-700 text-center max-w-2xl leading-relaxed mx-auto mb-6">
            En Tecnopro Soluciones, nos esforzamos por garantizar tu satisfacción. A continuación, te explicamos paso a paso cómo iniciar el proceso de devolución y reembolso para un producto adquirido:
        </p>

        <div class="space-y-6">
            <!-- Paso a paso -->
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Paso 1: Accede a tu cuenta</h2>
                <p class="text-gray-700 leading-relaxed">
                    Inicia sesión en tu cuenta con tu correo electrónico y contraseña. Dirígete a la sección de <strong>"Usuario"</strong> en el menú principal.
                </p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Paso 2: Revisa tus pedidos</h2>
                <p class="text-gray-700 leading-relaxed">
                    En la sección de usuario, selecciona <strong>"Mis Pedidos"</strong> para ver la lista de todos tus pedidos realizados.
                </p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Paso 3: Selecciona el pedido</h2>
                <p class="text-gray-700 leading-relaxed">
                    Haz clic en <strong>"Ver Detalles"</strong> en el pedido que contiene el producto que deseas devolver. Esto te llevará a la página de detalles del pedido.
                </p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Paso 4: Solicita la devolución</h2>
                <p class="text-gray-700 leading-relaxed">
                    En la página de detalles del pedido, selecciona la opción <strong>"Solicitar Devolución y Reembolso"</strong>. Luego:
                </p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed ml-4">
                    <li>Elige el producto que deseas devolver.</li>
                    <li>Especifica la cantidad de productos a devolver.</li>
                    <li>Describe los motivos de la devolución en el campo de texto proporcionado.</li>
                </ul>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Paso 5: Proceso de evaluación</h2>
                <p class="text-gray-700 leading-relaxed">
                    Una vez enviada tu solicitud, entrará en un proceso de evaluación. Nos aseguraremos de que el producto cumpla con las condiciones de devolución (en su empaque original, sin uso, con todos sus accesorios). Te notificaremos tan pronto como sea posible con la resolución de tu solicitud. Por favor, mantente pendiente de tu correo electrónico o la sección de pedidos en tu cuenta.
                </p>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">¿Qué hacer si no recibes respuesta?</h2>
                <p class="text-gray-700 leading-relaxed">
                    Si no recibes una respuesta dentro de los 10 días hábiles, te recomendamos enviar un correo a 
                    <a href="mailto:contactotecnoprosoluciones@gmail.com" class="text-blue-600 font-semibold hover:underline">contactotecnoprosoluciones@gmail.com</a> 
                    con los siguientes detalles:
                </p>
                <ul class="list-disc list-inside text-gray-700 leading-relaxed ml-4">
                    <li><strong>ID de usuario:</strong> Tu ID de usuario registrado en la plataforma.</li>
                    <li><strong>ID del pedido:</strong> El número del pedido relacionado con la devolución.</li>
                    <li><strong>Producto:</strong> Nombre del producto que deseas devolver.</li>
                    <li><strong>Cantidad:</strong> Cantidad de productos a devolver.</li>
                    <li><strong>Problema:</strong> Descripción detallada del inconveniente.</li>
                </ul>
            </div>
        </div>

        <!-- Botón a Tus Pedidos -->
        <div class="mt-6 text-center">
            <a href="{{ route('cuenta.pedidos') }}" class="bg-blue-600 text-white px-6 py-3 rounded-md w-full max-w-md text-center hover:bg-blue-700 flex items-center justify-center gap-3 mx-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Ir a Tus Pedidos
            </a>
        </div>
    </div>
</div>
@endsection
