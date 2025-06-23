@extends('layouts.layout')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8 space-y-8">
    <!-- Bienvenida -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-800 text-center mb-6">Bienvenidos a Tecnopro Soluciones</h1>
        <p class="text-gray-700 text-center max-w-2xl leading-relaxed mx-auto">
            Estimado cliente, le informamos que nuestra empresa está disponible las 24 horas del día los 7 días de la semana. Tecnopro Soluciones es una empresa dedicada a la comercialización de productos tecnológicos de alta calidad. No encontrarás otro lugar como este donde encontrarás artículos como laptops, accesorios y más, todo a precios competitivos. Contamos con un equipo de profesionales altamente capacitados que te brindarán la mejor atención y soporte técnico.
        </p>
    </div>

    <!-- Métodos de Pago -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 text-center mb-4">Métodos de Pago</h2>
        <p class="text-gray-700 text-center max-w-2xl leading-relaxed mx-auto">
            Ofrecemos una variedad de métodos de pago para tu conveniencia: tarjetas de crédito y débito (Visa, Mastercard), transferencias bancarias, y pagos en efectivo a través de agentes autorizados. Todos los pagos son procesados de forma segura con encriptación avanzada para proteger tu información financiera. Aceptamos pagos en soles (PEN) y ofrecemos opciones de financiación en cuotas sin intereses bajo ciertas condiciones.
        </p>
    </div>

    <!-- Contacto -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 text-center mb-4">Contacto</h2>
        <p class="text-gray-700 text-center max-w-2xl leading-relaxed mx-auto mb-4">
            Para cualquier consulta o soporte, no dudes en contactarnos. Nuestro equipo está listo para ayudarte. Puedes escribirnos al siguiente correo:
        </p>
        <p class="text-center text-blue-600 font-semibold hover:underline cursor-pointer" onclick="window.location.href='mailto:contactotecnoprosoluciones@gmail.com'">contactotecnoprosoluciones@gmail.com</p>
    </div>

    <!-- Devoluciones y Reembolso -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 text-center mb-4">Devoluciones y Reembolso</h2>
        <p class="text-gray-700 text-center max-w-2xl leading-relaxed mx-auto mb-4">
            En Tecnopro Soluciones, ofrecemos un plazo de 7 días para devoluciones a partir de la fecha de entrega, siempre que el producto se encuentre en su empaque original, sin uso y con todos sus accesorios. Los reembolsos se procesarán dentro de 10 días hábiles después de recibir el producto, utilizando el mismo método de pago original. Para productos defectuosos, contáctanos inmediatamente para coordinar la devolución.
        </p>
        <a href="{{route('devolucion')}}" class="bg-blue-600 text-white px-6 py-3 rounded-md w-full max-w-md text-center hover:bg-blue-700 flex items-center justify-center gap-3 mx-auto block">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
            </svg>
            Iniciar Proceso de Devolución
        </a>
    </div>
</div>
@endsection

