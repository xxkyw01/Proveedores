<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ProveedorAuthController;

use App\Http\Controllers\Proveedor\ProveedorMenuController;
use App\Http\Controllers\Proveedor\ReservacionController;
use App\Http\Controllers\Proveedor\CitaController;

use App\Http\Controllers\Almacen\GestionSupplierController;
use App\Http\Controllers\Almacen\TableroController;
use App\Http\Controllers\Almacen\ConfirmarCitaController;
use App\Http\Controllers\Almacen\CitaExpressController;
use App\Http\Controllers\Almacen\CitaNoProgramadaController;
use App\Http\Controllers\Almacen\DashboardController;

use App\Http\Controllers\Dev\UsuarioDevController;

use App\Http\Controllers\Compras\ReporteManiobrasController;
use App\Http\Controllers\Compras\CalendarioSPController;

use App\Http\Controllers\ChatController;
use App\Http\Controllers\Almacen\SapRecepcionController;

use App\Services\SAPServiceLayer; 


// =======================
//1. Página inicial: redirecciona al login
// =======================

    // Redirección general de login
    Route::get('/', fn() => redirect()->route('proveedor.login'))->name('login');

    // Grupo de rutas para proveedor
    Route::prefix('proveedor')->group(function () {

        // Mostrar formulario de login
        Route::get('/login', [ProveedorAuthController::class, 'showLoginForm'])->name('proveedor.login');

        // Procesar login
        Route::post('/login', [ProveedorAuthController::class, 'login'])->name('proveedor_login_post');

        // Redirección según sesión
        Route::get('/redirect', function () {
            if (session()->has('Usuario')) {
                $rol = session('Usuario.IdRol');
                return match ($rol) {
                    1 => redirect()->route('admin.dashboard'),
                    2 => redirect()->route('almacen.dashboard'),
                    3 => redirect()->route('compras.dashboard'),
                    4 => redirect()->route('mejora.dashboard'),
                    5 => redirect()->route('dev.dashboard'),
                    7 => redirect()->route('auditoria.dashboard'),
                    8 => redirect()->route('monitor.dashboard'),
                    default => redirect()->route('login'),
                };
            }

            if (session()->has('Proveedor')) {
                return redirect()->route('proveedor.dashboard');
            }

            return redirect()->route('login');
        })->name('proveedor.redirect');
    });

    // Logout general
    Route::post('/logout', [ProveedorAuthController::class, 'logout'])->name('logout');

// =======================
// 3.- RUTAS PARA PROVEEDORES
// =======================
Route::middleware(['proveedor', 'prevent-back-history'])->prefix('proveedor')->group(function () {
    Route::get('/dashboard', fn() => view('index_proveedor'))->name('proveedor.dashboard');
    Route::get('/menu', [ProveedorMenuController::class, 'menu'])->name('proveedor.menu');

    //3.1- Solicitar Cita
    Route::get('/citas', [CitaController::class, 'index'])->name('proveedor.citas');
    Route::get('/obtener-estado/{id}', [CitaController::class, 'obtenerEstado']);
    Route::get('/obtener-sucursales/{id}', [CitaController::class, 'obtenerSucursales']);
    Route::get('/datos/{codigo}', [CitaController::class, 'obtenerDatosProveedor']);
    Route::get('/obtener-andenes/{id}', [CitaController::class, 'obtenerAndenes']);
    Route::get('/serie-oc/{sucursal_id}', [CitaController::class, 'obtenerSerieOC']);
    Route::get('/precio-caja/{codigoProveedor}', [CitaController::class, 'obtenerPrecioPorCaja']);
    Route::post('/citas', [CitaController::class, 'store'])->name('proveedor.citas.store');
    Route::post('/citas/disponibilidad', [CitaController::class, 'getDisponibilidad'])->name('proveedor.disponibilidad');
    Route::post('/ordenes', [CitaController::class, 'obtenerOrdenesCompra']);
    Route::post('/articulos', [CitaController::class, 'obtenerArticulosPendientes']);
    Route::post('/enviar-solicitud', [CitaController::class, 'enviarSolicitudCorreo']);

    //3.2- Historial
    Route::get('/historial', [ReservacionController::class, 'historial'])->name('proveedor.historial');
    Route::get('/reporte/pdf', [ReservacionController::class, 'generarPDF']);
    Route::post('/cancelacion/solicitar', [ReservacionController::class, 'solicitarCancelacion']);
    Route::post('/cancelacion/confirmar', [ReservacionController::class, 'confirmarCancelacion']);
    Route::get('/evidencia/{id}', [ReservacionController::class, 'verEvidencia'])->name('proveedor.evidencia');

    // 3.3 - Mensajería
    Route::get('/mensajeria', [ChatController::class, 'index'])->name('proveedor.mensajeria');
    Route::get('/mensajeria/mensajes/{tipo}/{id}', [ChatController::class, 'fetch'])->name('proveedor.mensajeria.fetch');
    Route::post('/mensajeria/enviar', [ChatController::class, 'send'])->name('proveedor.mensajeria.send');
    Route::get('/mensajeria/chats', [ChatController::class, 'chatsRecientes']);
    Route::get('/mensajeria/leido', [ChatController::class, 'marcarComoLeido'])->name('proveedor.mensajeria.leido');
});

// =======================
// 4.- RUTAS PARA ALMACÉN
// =======================
Route::middleware(['almacen', 'prevent-back-history'])->prefix('almacen')->group(function () {

    Route::get('/dashboard', fn() => view('index_almacen'))->name('almacen.dashboard');
    Route::get('/menu', [ProveedorMenuController::class, 'menu'])->name('almacen.menu');

    //4.1- Agenda
    Route::get('/AgendaProveedor', [GestionSupplierController::class, 'index'])->name('proveedor.AgendaProveedor');
    Route::get('/agenda', [GestionSupplierController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/data', [GestionSupplierController::class, 'getAgendaData'])->name('agenda.data');
    Route::get('/agenda/detalles/{id}', [GestionSupplierController::class, 'getDetails'])->name('agenda.detalles');
    Route::get('/agenda/articulos/{orden}', [GestionSupplierController::class, 'getArticulosPendientes'])->name('agenda.articulos');
    Route::post('/agenda/actualizar-estado', [GestionSupplierController::class, 'actualizarEstado']);
    Route::get('/agenda/tarjetas', [GestionSupplierController::class, 'agendaParcial'])->name('almacen.agenda.tarjetas');

    //4.2- Tablero de disponibilidad
    Route::get('tablero', [TableroController::class, 'index'])->name('almacen.tablero.index');
    Route::get('/tablero/mostrar', [TableroController::class, 'mostrar'])->name('almacen.tablero.mostrar');
    Route::get('/tablero/tabla', [TableroController::class, 'actualizarTabla'])->name('almacen.tablero.tabla');
    Route::get('/tablero', fn() => redirect()->route('almacen.tablero.mostrar', ['sucursal_id' => 1]))->name('almacen.tablero');
    Route::get('/almacen/tablero/parcial', [TableroController::class, 'parcial'])->name('almacen.tablero.parcial');

    //4.3- Confirmar cita
    Route::get('/confirmarCita', [ConfirmarCitaController::class, 'index'])->name('almacen.confirmarCitas');
    Route::get('/confirmarCita/detalle/{id}', [ConfirmarCitaController::class, 'detalleCita']);
    Route::post('/confirmarCita/actualizarEstado', [ConfirmarCitaController::class, 'actualizarEstado']);
    Route::get('/confirmarCita/pendientes', [ConfirmarCitaController::class, 'obtenerPendientes'])->name('almacen.confirmarCitas.pendientes');
    Route::get('/confirmarCita/ayer', [ConfirmarCitaController::class, 'obtenerAyer'])->name('almacen.confirmarCitas.ayer');
    Route::get('/confirmarCita/semana', [ConfirmarCitaController::class, 'obtenerSemana'])->name('almacen.confirmarCitas.semana');
    Route::get('/confirmarCita/mes', [ConfirmarCitaController::class, 'obtenerMes'])->name('almacen.confirmarCitas.mes');

    // 4.4 - Mensajería
    Route::get('/mensajeria', [ChatController::class, 'index'])->name('almacen.mensajeria');
    Route::get('/mensajeria/mensajes/{tipo}/{id}', [ChatController::class, 'fetch'])->name('almacen.mensajeria.fetch');
    Route::post('/mensajeria/enviar', [ChatController::class, 'send'])->name('almacen.mensajeria.send');
    Route::get('/mensajeria/chats', [ChatController::class, 'chatsRecientes']);
    Route::post('/mensajeria/leido', [ChatController::class, 'marcarComoLeido'])->name('almacen.mensajeria.leido');

    //4.5- Cita  Express
    Route::get('/cita-express', [CitaExpressController::class, 'index']);
    Route::post('/cita-express/registrar', [CitaExpressController::class, 'registrar']);
    Route::get('/cita-express/lista', [CitaExpressController::class, 'lista']);
    Route::post('/cita-express/estado/{id}/{estado}', [CitaExpressController::class, 'estado']);
    Route::get('/cita-express/sucursales/{id}', [CitaExpressController::class, 'obtenerSucursales']);

    // 4.6 - Cita Apartado
    Route::get('/cita-apartado', [\App\Http\Controllers\Almacen\CitaApartadoController::class, 'index'])->name('almacen.citaApartado.index');
    Route::post('/cita-apartado/disponibilidad', [\App\Http\Controllers\Almacen\CitaApartadoController::class, 'disponibilidad'])->name('almacen.citaApartado.disponibilidad');
    Route::post('/cita-apartado', [\App\Http\Controllers\Almacen\CitaApartadoController::class, 'store'])->name('almacen.citaApartado.store');
    // auxiliares
    Route::get('/cita-apartado/sucursales/{entidad_id}', [\App\Http\Controllers\Almacen\CitaApartadoController::class, 'obtenerSucursales']);
    Route::get('/cita-apartado/andenes/{sucursal_id}', [\App\Http\Controllers\Almacen\CitaApartadoController::class, 'obtenerAndenes']);

    //4.7 Cita No Programada
    Route::get('/citas-no-programadas', [CitaNoProgramadaController::class, 'index'])->name('evento.citas');
    Route::get('/obtener-estado/{id}', [CitaNoProgramadaController::class, 'obtenerEstado']);
    Route::get('/obtener-sucursales/{id}', [CitaNoProgramadaController::class, 'obtenerSucursales']);
    Route::get('/datos/{codigo}', [CitaNoProgramadaController::class, 'obtenerDatosProveedor']);
    Route::get('/obtener-andenes/{id}', [CitaNoProgramadaController::class, 'obtenerAndenes']);
    Route::get('/serie-oc/{sucursal_id}', [CitaNoProgramadaController::class, 'obtenerSerieOC']);

    Route::post('/citas', [CitaNoProgramadaController::class, 'store'])->name('almacen.citas.store');
    Route::post('/citas/disponibilidad', [CitaNoProgramadaController::class, 'getDisponibilidad'])->name('evento.citas.disponibilidad');

    //4.8 Dashboard
    Route::get('/KPIDashboard', [DashboardController::class, 'KPIDashboard'])->name('dashboard.kpis');

    //4.9 SAP Recepción
    Route::get('/recepcion/po/{docNum}',[GestionSupplierController::class, 'sapGetPO']);
    Route::post('/recepcion/grpo/validar', [GestionSupplierController::class, 'sapValidarGRPO'])->name('almacen.grpo.validar');
    Route::post('/recepcion/grpo', [GestionSupplierController::class, 'crearGRPO'])->name('almacen.grpo.crear');

    //4.10 SAP Consulta de Ordenes de Compra
    Route::get('/recibo-mercancia', [GestionSupplierController::class, 'reciboMercancia'])->name('almacen.reciboMercancia');

    //4.11 SAP Consulta de Entradas de Mercancia
    // routes/web.php
    Route::post('reservacion/{id}/agregar-oc', [GestionSupplierController::class, 'agregarOCReservacion']);
    Route::get('reservacion/{id}/ocs', [GestionSupplierController::class, 'ocsDeReservacion']);
    Route::get('reservacion/{id}/ocs-disponibles', [GestionSupplierController::class, 'ocsDisponiblesParaReservacion']);

});
// =======================
// 5.- RUTAS PARA COMPRAS
// =======================
Route::middleware(['compras', 'prevent-back-history'])->prefix('compras')->group(function () {

    Route::get('/dashboard', fn() => view('index_compras'))->name('compras.dashboard');
    Route::get('/menu', [ProveedorMenuController::class, 'menu'])->name('compras.menu');

    // 5.1- Reporte de Maniobras
    Route::get('/reporte/maniobras', [ReporteManiobrasController::class, 'reporteManiobras'])->name('reporte.maniobras');
    Route::get('/reporte/maniobras/export', [ReporteManiobrasController::class, 'exportExcel'])->name('reporte.maniobras.export');

    //5.2- Calendario de los Proveedores(Disponibilidad)
    Route::get('/calendario', [CalendarioSPController::class, 'index'])->name('compras.calendario.index');
    Route::get('/calendario/disponibilidad', [CalendarioSPController::class, 'mostrarDisponibilidad'])->name('compras.calendario.disponibilidad');

    // 5.3 - Mensajería
    Route::get('/mensajeria', [ChatController::class, 'index'])->name('compras.mensajeria');
    Route::get('/mensajeria/mensajes/{tipo}/{id}', [ChatController::class, 'fetch'])->name('compras.mensajeria.fetch');
    Route::post('/mensajeria/enviar', [ChatController::class, 'send'])->name('compras.mensajeria.send');
    Route::get('/mensajeria/chats', [ChatController::class, 'chatsRecientes']);
    Route::post('/mensajeria/leido', [ChatController::class, 'marcarComoLeido'])->name('compras.mensajeria.leido');
});

// =======================s
// 6.- RUTAS PARA ADMINISTRADOR
// =======================
Route::middleware(['admin', 'prevent-back-history'])->prefix('admin')->group(function () {

    Route::get('/dashboard', fn() => view('index_admin'))->name('admin.dashboard');

    // 6.1 - Mensajería
    Route::get('/mensajeria', [ChatController::class, 'index'])->name('admin.mensajeria');
    Route::get('/mensajeria/mensajes/{tipo}/{id}', [ChatController::class, 'fetch'])->name('admin.mensajeria.fetch');
    Route::post('/mensajeria/enviar', [ChatController::class, 'send'])->name('admin.mensajeria.send');
    Route::get('/mensajeria/chats', [ChatController::class, 'chatsRecientes']);
    Route::post('/mensajeria/leido', [ChatController::class, 'marcarComoLeido'])->name('admin.mensajeria.leido');
});

// =======================
// 7.- RUTAS PARA DEV
// =======================
Route::middleware(['dev', 'prevent-back-history'])->prefix('dev')->group(function () {

    Route::get('/dashboard', fn() => view('index_dev'))->name('dev.dashboard');

    //7.1- Consultas Ususuario
    Route::get('/crear-usuario', [UsuarioDevController::class, 'formulario'])->name('dev.formulario');
    Route::get('/usuarios', [UsuarioDevController::class, 'listarUsuarios'])->name('dev.usuarios');
    Route::post('/usuarios/actualizar-password', [UsuarioDevController::class, 'cambiarPasswordUsuario'])->name('dev.usuario.password');
    Route::post('/crear-usuario', [UsuarioDevController::class, 'crear'])->name('dev.crear');

    //7.2 - Mensajería
    Route::get('/mensajeria', [ChatController::class, 'index'])->name('dev.mensajeria');
    Route::get('/mensajeria/mensajes/{tipo}/{id}', [ChatController::class, 'fetch'])->name('dev.mensajeria.fetch');
    Route::post('/mensajeria/enviar', [ChatController::class, 'send'])->name('dev.mensajeria.send');
    Route::get('/mensajeria/chats', [ChatController::class, 'chatsRecientes']);
    Route::post('/mensajeria/leido', [ChatController::class, 'marcarComoLeido'])->name('dev.mensajeria.leido');
});

// ================================
// 8.- RUTAS PARA MEJORA CONTINUA
// ================================
Route::middleware(['mejora', 'prevent-back-history'])->prefix('mejora')->group(function () {

    Route::get('/dashboard', fn() => view('index_mejora'))->name('mejora.dashboard');

    //8.2 - Mensajería
    Route::get('/mensajeria', [ChatController::class, 'index'])->name('mejora.mensajeria');
    Route::get('/mensajeria/mensajes/{tipo}/{id}', [ChatController::class, 'fetch'])->name('mejora.mensajeria.fetch');
    Route::post('/mensajeria/enviar', [ChatController::class, 'send'])->name('mejora.mensajeria.send');
    Route::get('/mensajeria/chats', [ChatController::class, 'chatsRecientes']);
    Route::post('/mensajeria/leido', [ChatController::class, 'marcarComoLeido'])->name('mejora.mensajeria.leido');
});

// =======================
// 9.- RUTAS PARA AUDITORIAS
// =======================
Route::middleware(['auditoria', 'prevent-back-history'])->prefix('auditoria')->group(function () {

    Route::get('/dashboard', fn() => view('index_auditoria'))->name('auditoria.dashboard');
});

// =======================
// 10.- RUTAS PARA MONITOR 
// =======================
Route::middleware(['monitor', 'prevent-back-history'])->prefix('monitor')->group(function () {
    Route::get('/dashboard', fn() => view('index_monitor'))->name('monitor.dashboard');
});

