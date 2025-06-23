<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\configuracion\ConfigController;
use App\Http\Controllers\configuracion\EmpleadoController;
use App\Http\Controllers\configuracion\CargosController;
use App\Http\Controllers\configuracion\CategoriasController;
use App\Http\Controllers\configuracion\GeneroController;
use App\Http\Controllers\configuracion\TipoDocumentoController;
use App\Http\Controllers\configuracion\EstadoPedidoController;
use App\Http\Controllers\configuracion\EstadoSolicitudController;
use App\Http\Controllers\configuracion\EstadoSolDevReemController;
use App\Http\Controllers\configuracion\PaisController;
use App\Http\Controllers\configuracion\DepartamentoController;
use App\Http\Controllers\configuracion\MunicipioController;
use App\Http\Controllers\configuracion\ProveedorController;
use App\Http\Controllers\configuracion\ProductoController;
use App\Http\Controllers\configuracion\FormaPagoController;
use App\Http\Controllers\catalogo\CatalogoController;
use App\Http\Controllers\catalogo\ComentarioController;
use App\Http\Controllers\carrito\CarritoController;
use App\Http\Controllers\carrito\ConfirmarPedido;
use App\Http\Controllers\kardex\KardexController;
use App\Http\Controllers\carrito\ConfirmarPedidoController;
use App\Http\Controllers\usuario\UsuarioController;
use App\Http\Controllers\configuracion\AdminController;

Route::get('/index', [IndexController::class, 'index'])->name('index')->middleware('web');
Route::get('/atencion-cliente', function () { return view('atencionAlCliente.atencionAlCliente'); })->name('support')->middleware('web');
Route::get('/carrito', function () { return view('carritoDeCompras.carrito'); })->name('carrito')->middleware('auth');
Route::get('/inventory', function () { return view('inventory'); })->name('inventory')->middleware('auth');
Route::get('/bienvenidos', function () {return view('atencionAlCliente.bienvenidos');})->name('bienvenidos');
Route::get('/devolucion', function () {return view('atencionAlCliente.devolucion');})->name('devolucion');

Route::get('/config', [ConfigController::class, 'index'])->name('config')->middleware('auth');
Route::get('/config/generador_pedidos', [ConfigController::class, 'generadorPedidos'])->name('config.generador_pedidos')->middleware('auth');
Route::get('/config/estado', [ConfigController::class, 'estado'])->name('config.estado')->middleware('auth');

Route::get('/kardex/{idProducto?}', [KardexController::class, 'index'])->name('kardex.index');
Route::get('/kardex/create', [KardexController::class, 'create'])->name('kardex.create');
Route::post('/kardex', [KardexController::class, 'store'])->name('kardex.store');

Route::middleware(['auth'])->group(function () {

    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/lista-json', [EmpleadoController::class, 'listaJson'])->name('empleados.listaJson');
    Route::get('/empleados/buscarUsuario', [EmpleadoController::class, 'buscarUsuario'])->name('empleados.buscarUsuario');
    Route::get('/empleados/buscarEmpleado', [EmpleadoController::class, 'buscarEmpleado'])->name('empleados.buscarEmpleado');
    Route::get('/empleados/{empleado}', [EmpleadoController::class, 'show'])->name('empleados.show');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::put('/empleados/{empleado}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{empleado}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
 
});

Route::get('/cargos', [CargosController::class, 'index'])->name('cargos.index');
Route::get('/cargos/buscarCargo', [CargosController::class, 'buscarCargo'])->name('cargos.buscarCargo');
Route::post('/cargos', [CargosController::class, 'store'])->name('cargos.store');
Route::put('/cargos/{idCargo}', [CargosController::class, 'update'])->name('cargos.update');
Route::delete('/cargos/{cargo}', [CargosController::class, 'destroy'])->name('cargos.destroy');

Route::group(['prefix' => 'categorias', 'as' => 'categorias.'], function () {
    Route::get('/', [CategoriasController::class, 'index'])->name('index');
    Route::get('/buscarCategoria', [CategoriasController::class, 'buscarCategoria'])->name('buscarCategoria');
    Route::post('/', [CategoriasController::class, 'store'])->name('store');
    Route::put('/{idCategoria}', [CategoriasController::class, 'update'])->name('update');
    Route::delete('/{categoria}', [CategoriasController::class, 'destroy'])->name('destroy');
});

Route::group(['prefix' => 'generos', 'as' => 'generos.'], function () {
        Route::get('/', [GeneroController::class, 'index'])->name('index');
        Route::get('/buscarGenero', [GeneroController::class, 'buscarGenero'])->name('buscarGenero');
        Route::post('/', [GeneroController::class, 'store'])->name('store');
        Route::put('/{idGenero}', [GeneroController::class, 'update'])->name('update');
        Route::delete('/{genero}', [GeneroController::class, 'destroy'])->name('destroy');
    });

Route::prefix('tipos_documento')->name('tipos_documento.')->group(function () {
    Route::get('/', [TipoDocumentoController::class, 'index'])->name('index');
    Route::get('/buscarTipoDocumento', [TipoDocumentoController::class, 'buscarTipoDocumento'])->name('buscarTipoDocumento');
    Route::post('/', [TipoDocumentoController::class, 'store'])->name('store');
    Route::put('/{idTipoDocumento}', [TipoDocumentoController::class, 'update'])->name('update');
    Route::delete('/{tipoDocumento}', [TipoDocumentoController::class, 'destroy'])->name('destroy');
});


Route::group(['prefix' => 'estados_pedido', 'as' => 'estados_pedido.'], function () {
    Route::get('/', [EstadoPedidoController::class, 'index'])->name('index');
    Route::get('/buscarEstado', [EstadoPedidoController::class, 'buscarEstado'])->name('buscarEstado');
    Route::post('/', [EstadoPedidoController::class, 'store'])->name('store');
    Route::put('/{idEstadoPedido}', [EstadoPedidoController::class, 'update'])->name('update');
    Route::delete('/{estado}', [EstadoPedidoController::class, 'destroy'])->name('destroy');
});

Route::group(['prefix' => 'estados_solicitud', 'as' => 'estados_solicitud.'], function () {
    Route::get('/', [EstadoSolDevReemController::class, 'index'])->name('index');
    Route::get('/buscarEstado', [EstadoSolDevReemController::class, 'buscarEstado'])->name('buscarEstado');
    Route::post('/', [EstadoSolDevReemController::class, 'store'])->name('store');
    Route::put('/{idEstadoSolDevReem}', [EstadoSolDevReemController::class, 'update'])->name('update');
    Route::delete('/{estado}', [EstadoSolDevReemController::class, 'destroy'])->name('destroy');
});

Route::group(['prefix' => 'paises', 'as' => 'paises.'], function () {
    Route::get('/', [PaisController::class, 'index'])->name('index');
    Route::get('/buscarPais', [PaisController::class, 'buscarPais'])->name('buscarPais');
    Route::post('/', [PaisController::class, 'store'])->name('store');
    Route::put('/{idPais}', [PaisController::class, 'update'])->name('update');
    Route::delete('/{pais}', [PaisController::class, 'destroy'])->name('destroy');
});


Route::group(['prefix' => 'departamentos', 'as' => 'departamentos.'], function () {
    Route::get('/', [DepartamentoController::class, 'index'])->name('index');
    Route::get('/buscarDepartamento', [DepartamentoController::class, 'buscarDepartamento'])->name('buscarDepartamento');
    Route::post('/', [DepartamentoController::class, 'store'])->name('store');
    Route::put('/{idDepartamento}', [DepartamentoController::class, 'update'])->name('update');
    Route::delete('/{departamento}', [DepartamentoController::class, 'destroy'])->name('destroy');
});

Route::group(['prefix' => 'municipios', 'as' => 'municipios.'], function () {
    Route::get('/', [MunicipioController::class, 'index'])->name('index');
    Route::get('/buscarMunicipio', [MunicipioController::class, 'buscarMunicipio'])->name('buscarMunicipio');
    Route::post('/', [MunicipioController::class, 'store'])->name('store');
    Route::put('/{idMunicipio}', [MunicipioController::class, 'update'])->name('update');
    Route::delete('/{municipio}', [MunicipioController::class, 'destroy'])->name('destroy');
});
Route::get('/', function () {
    return view('index');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['prefix' => 'proveedores', 'as' => 'proveedores.'], function () {
    Route::get('/', [ProveedorController::class, 'index'])->name('index');
    Route::get('/buscarProveedor', [ProveedorController::class, 'buscarProveedor'])->name('buscarProveedor');
    Route::post('/', [ProveedorController::class, 'store'])->name('store');
    Route::put('/{idProveedor}', [ProveedorController::class, 'update'])->name('update');
    Route::delete('/{proveedor}', [ProveedorController::class, 'destroy'])->name('destroy');
});

Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::get('/productos/lista-json', [ProductoController::class, 'listaJson'])->name('productos.listaJson');
Route::get('/productos/buscarProducto', [ProductoController::class, 'buscarProducto'])->name('productos.buscarProducto');
Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');

Route::get('/formas-pago', [FormaPagoController::class, 'index'])->name('formas-pago.index');
Route::get('/formas-pago/buscarFormaPago', [FormaPagoController::class, 'buscarFormaPago'])->name('formas-pago.buscarFormaPago');
Route::post('/formas-pago', [FormaPagoController::class, 'store'])->name('formas-pago.store');
Route::put('/formas-pago/{idFormaPago}', [FormaPagoController::class, 'update'])->name('formas-pago.update');
Route::delete('/formas-pago/{idFormaPago}', [FormaPagoController::class, 'destroy'])->name('formas-pago.destroy');

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/catalogo/{producto}', [CatalogoController::class, 'show'])->name('catalogo.show');

Route::post('/catalogo/{idProducto}/comentarios', [ComentarioController::class, 'store'])->name('comentarios.store');
Route::put('/catalogo/{idProducto}/comentarios/{idComentario}', [ComentarioController::class, 'update'])->name('comentarios.update');
Route::delete('/catalogo/{idProducto}/comentarios/{idComentario}', [ComentarioController::class, 'destroy'])->name('comentarios.destroy');


Route::get('/create', [KardexController::class, 'create'])->name('kardex.create');
Route::get('/kardex/{idProducto?}', [KardexController::class, 'index'])->name('kardex.index');
Route::post('/kardex', [KardexController::class, 'store'])->name('kardex.store');

Route::post('/carrito/actualizar-direccion', [CarritoController::class, 'actualizarDireccion'])->name('carrito.actualizarDireccion');
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::get('/carrito/confirmar', [CarritoController::class, 'confirmar'])->name('carrito.confirmar');
Route::post('/carrito/confirmar', [CarritoController::class, 'confirmar'])->name('carrito.confirmar');
Route::get('/carrito/stock/{idProducto}', [CarritoController::class, 'getStock'])->name('carrito.stock');
Route::post('/carrito/store', [CarritoController::class, 'storeCarrito'])->name('carrito.store');
Route::post('/carrito/procesar-compra', [CarritoController::class, 'procesarCompra'])->name('carrito.procesarCompra');
Route::get('exito', [CarritoController::class, 'exito'])->name('carrito.exito'); // Agrega esta línea

Route::prefix('cuenta')->middleware('auth')->group(function () {
    Route::get('/', [UsuarioController::class, 'perfil'])->name('cuenta.perfil');
    Route::get('/editar', [UsuarioController::class, 'editar'])->name('cuenta.editar');
    Route::post('/actualizar', [UsuarioController::class, 'actualizar'])->name('cuenta.actualizar');
    Route::get('/pedidos', [UsuarioController::class, 'pedidos'])->name('cuenta.pedidos');
    Route::get('/pedidos/{id}', [UsuarioController::class, 'detallePedido'])->name('cuenta.detalle-pedido');
    Route::get('/solicitudes-devolucion', [UsuarioController::class, 'solicitudesDevolucion'])->name('cuenta.solicitudes-devolucion');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/admin/pedidos', [AdminController::class, 'pedidos'])->name('admin.pedidos');
    Route::get('/admin/pedidos/search', [AdminController::class, 'pedidosSearch'])->name('admin.pedidos.search');
    Route::get('/admin/pedidos/{idPedido}', [AdminController::class, 'detallePedidoAdmin'])->name('admin.detalle-pedido');
    Route::put('/admin/pedidos/{idPedido}/estado', [AdminController::class, 'actualizarEstadoPedido'])->name('admin.actualizar-estado-pedido');
    Route::get('/admin/solicitudes', [AdminController::class, 'solicitudes'])->name('admin.solicitudes');
    Route::get('/admin/solicitudes/search', [AdminController::class, 'solicitudesSearch'])->name('admin.solicitudes.search');
    Route::get('/admin/solicitudes/pedido/{idPedido}', [AdminController::class, 'detallePedidoSolicitud'])->name('admin.detalle-pedido-solicitud');
    Route::get('/admin/solicitudes/{idSolDevReem}', [AdminController::class, 'detalleSolicitudAdmin'])->name('admin.detalle-solicitud');
    Route::put('/admin/solicitudes/{idSolDevReem}/estado', [AdminController::class, 'actualizarEstadoSolicitud'])->name('admin.actualizar-estado-solicitud');
});
Route::post('/devolucion/procesar', [UsuarioController::class, 'procesarDevolucion'])->name('cuenta.procesar-devolucion');

Route::get('/departamentos/{idPais}', [UbicacionController::class, 'getDepartamentos'])->name('ubicacion.departamentos');
Route::get('/municipios/{idDepartamento}', [UbicacionController::class, 'getMunicipios'])->name('ubicacion.municipios');


Route::get('/dashboard', function () {
    return view('index');
})->name('dashboard');


require __DIR__.'/auth.php';
