@extends('layouts.movil')
@section('title', 'Mensajes')
@section('content')
    @include('includes.scripts.bootstrap')
    @include('includes.scripts.Socketio')
    <link rel="stylesheet" href="{{ asset('assets/css/rol/socket/mensajeria.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <div class="container">
        <div class="row h-100">
            <!-- Sidebar -->
            <div class="col-md-4 border-end d-flex flex-column p-0 bg-light">
                <div class="p-3 border-bottom bg-light">
                    <input type="text" id="busquedaChat" class="form-control bg-secondary" placeholder="Buscar...">
                </div>
                <div class="flex-grow-1 overflow-auto" id="chatsRecientes">
                    <div class="text-center text-muted mt-5">Cargando chats...</div>
                </div>
                <button class="btn btn-primary  chat-boton-flotante" onclick="mostrarContactos()">
                    <i class="bi bi-people-fill"></i>
                </button>
            </div>

            <!-- Chat Window -->
            <div class="col-md-8 d-flex flex-column p-0 bg-white">
                <div class="p-3 border-bottom d-flex align-items-center bg-light">
                    <button class="btn btn-outline-secondary me-2 d-none" id="btnVolver" onclick="salirDelChat()">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <strong id="nombre-contacto">Selecciona un contacto</strong>
                </div>
                <div id="introMensaje" class="intro">
                    <h5>Tus mensajes</h5>
                    <p>Para iniciar tu conversacion debes seleccion chat existen o dar clic en el boton de "Enviar mensaje"
                        .</p>
                    <button class="btn btn-orange" onclick="mostrarContactos()"> Nuevo Chat</button>
                </div>
                <div id="mensajes" class="flex-grow-1 p-3 d-flex flex-column overflow-auto" style="display: none;"></div>
                <div id="contenedorFormulario" class="p-3 border-top bg-light" style="display: none;">
                    <form onsubmit="return enviarMensaje();" class="d-flex">
                        <input type="text" id="mensajeInput" class="form-control me-2"
                            placeholder="Escribe un mensaje...">
                        <button class="btn btn-orange" type="submit">Enviar</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!--MODAL -->
    <div class="modal fade" id="modalContactos" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content shadow">

                <!-- HEADER -->
                <div class="modal-header bg-orange text-white">
                    <h5 class="modal-title"><i class="bi bi-chat-dots-fill me-2"></i>Selecciona un contacto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY CON SCROLL -->
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <input type="text" id="buscadorContactos" class="form-control mb-3"
                        placeholder="Buscar por nombre...">

                    <!-- Mensaje si no hay coincidencias -->
                    <div id="mensaje-vacio" class="text-center text-muted my-2" style="display: none;">
                        No se encontraron resultados
                    </div>

                    {{-- <h6 class="text-primary mt-3"><i class="bi bi-person-badge-fill me-2"></i>Usuarios Internos</h6> --}}
                    <ul id="lista-usuarios" class="list-group mb-4">
                        @foreach ($contactos->where('tipo', 'U') as $c)
                            <li class="list-group-item contacto d-flex justify-content-between align-items-center"
                                data-id="{{ $c->id }}" data-tipo="U" data-nombre="{{ strtolower($c->Nombre) }}">
                                <div><i class="bi bi-person-circle me-2 text-primary"></i>{{ $c->Nombre }}</div>
                                <span class="badge bg-primary">Usuario</span>
                            </li>
                        @endforeach
                    </ul>

                    {{-- <h6 class="text-warning"><i class="bi bi-truck-front-fill me-2"></i>Proveedores</h6> --}}
                    <ul id="lista-proveedores" class="list-group">
                        @foreach ($contactos->where('tipo', 'P') as $c)
                            <li class="list-group-item contacto d-flex justify-content-between align-items-center"
                                data-id="{{ $c->id }}" data-tipo="P" data-nombre="{{ strtolower($c->Nombre) }}">
                                <div><i class="bi bi-truck me-2 text-warning"></i>{{ $c->Nombre }}</div>
                                <span class="badge bg-warning text-dark">Proveedor</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer d-flex justify-content-between">
                    <span class="text-muted" id="totalContactos">Total: {{ count($contactos) }}
                        contacto{{ count($contactos) !== 1 ? 's' : '' }}</span>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        const rutaBase = '{{ url()->current() }}'.split('/mensajeria')[0];
        const remitenteTipo = '{{ $es_proveedor ? 'P' : 'U' }}';
        const remitenteId = '{{ $es_proveedor ? session('Proveedor.id') : session('Usuario.IdUsuario') }}';
        let contactoSeleccionado = null;
        let tipoContacto = null;

        function mostrarContactos() {
            const modalElement = document.getElementById('modalContactos');
            if (!modalElement) return console.error('Modal no encontrado');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }

        function salirDelChat() {
            contactoSeleccionado = null;
            tipoContacto = null;

            // Ocultar chat y mostrar introducción
            const divMensajes = document.getElementById('mensajes');
            divMensajes.style.display = 'none';
            divMensajes.innerHTML = '';

            document.getElementById('introMensaje').style.display = 'flex';
            document.getElementById('nombre-contacto').innerText = 'Selecciona un contacto';
            document.getElementById('btnVolver').classList.add('d-none');
            document.getElementById('mensajeInput').value = '';
            document.getElementById('contenedorFormulario').style.display = 'none';
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') salirDelChat();
        });

        function cargarChats() {
            fetch(`${rutaBase}/mensajeria/chats`).then(res => res.json()).then(data => {
                let html = '';
                data.forEach(c => {
                    const burbuja = c.no_leidos > 0 ?
                        `<span class="badge bg-success rounded-pill ms-2">${c.no_leidos}</span>` : '';
                    html += `
    <div class="p-3 border-bottom chat-item d-flex justify-content-between align-items-center" 
        data-id="${c.contacto_id}" data-tipo="${c.contacto_tipo}" data-nombre="${c.contacto_nombre.toLowerCase()}">
        <div>
            <strong>${c.contacto_tipo == 'P' ? '🚚 ' : '🥐 '}${c.contacto_nombre}</strong>
            <div class="text-muted small">${c.ultimo_mensaje}</div>
        </div>
        ${burbuja}
    </div>`;

                });
                document.getElementById('chatsRecientes').innerHTML = html;
                document.querySelectorAll('.chat-item').forEach(e => {
                    e.addEventListener('click', function() {
                        contactoSeleccionado = this.dataset.id;
                        tipoContacto = this.dataset.tipo;
                        document.getElementById('nombre-contacto').innerText = this.querySelector(
                            'strong').innerText;
                        document.getElementById('introMensaje').style.display = 'none';
                        document.getElementById('mensajes').style.display = 'flex';
                        document.getElementById('btnVolver').classList.remove('d-none');
                        document.getElementById('contenedorFormulario').style.display = 'block';

                        cargarMensajes();
                    });
                });
            });
        }

        function cargarMensajes() {
            if (!contactoSeleccionado || !tipoContacto) return;
            fetch(`${rutaBase}/mensajeria/mensajes/${tipoContacto}/${contactoSeleccionado}`).then(res => res.json()).then(
                data => {
                    let mensajesHTML = '';
                    let fechaAnterior = '';
                    data.forEach(m => {
                        const fecha = new Date(m.fecha_envio).toLocaleDateString();
                        const hora = new Date(m.fecha_envio).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        if (fecha !== fechaAnterior) {
                            mensajesHTML += `<div class="text-center text-muted my-2 small">${fecha}</div>`;
                            fechaAnterior = fecha;
                        }
                        const esMio = m.remitente_tipo === remitenteTipo && m.remitente_id == remitenteId;
                        const clase = esMio ? 'mensaje-propio' : 'mensaje-ajeno';
                        mensajesHTML += `
                <div class="d-flex ${esMio ? 'justify-content-end' : 'justify-content-start'}">
                    <div class="mensaje-burbuja ${clase}">
                        ${m.mensaje}
                        <div class="small text-end text-muted" ">${hora}</div>
                    </div>
                </div>`;
                    });
                    document.getElementById('mensajes').innerHTML = mensajesHTML;
                    scrollAbajo();
                });
        }

        function enviarMensaje() {
            const mensaje = document.getElementById('mensajeInput').value.trim();
            if (!mensaje || !contactoSeleccionado) return false;
            fetch(`${rutaBase}/mensajeria/enviar`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    receptor_id: contactoSeleccionado,
                    receptor_tipo: tipoContacto,
                    mensaje: mensaje,
                    remitente_tipo: remitenteTipo
                })
            }).then(() => {
                document.getElementById('mensajeInput').value = '';
                cargarMensajes();
                cargarChats();
            });
            return false;
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.contacto').forEach(c => {
                c.addEventListener('click', function() {
                    contactoSeleccionado = this.dataset.id;
                    tipoContacto = this.dataset.tipo;
                    document.getElementById('nombre-contacto').innerText = this.innerText.trim();
                    document.getElementById('introMensaje').style.display = 'none';
                    document.getElementById('mensajes').style.display = 'flex';
                    document.getElementById('btnVolver').classList.remove('d-none');
                    document.getElementById('contenedorFormulario').style.display =
                        'block';

                    // limpiar los mensajes no leidos cunado abro el chat! 
                    marcarComoLeido(contactoSeleccionado, tipoContacto);

                    cargarMensajes();
                    bootstrap.Modal.getInstance(document.getElementById('modalContactos')).hide();
                });
            });

        });

        function marcarComoLeido(contactoId, tipo) {
            fetch(`${rutaBase}/mensajeria/leido`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    contacto_id: contactoId,
                    contacto_tipo: tipo
                })
            }).then(() => cargarChats());
        }

        function scrollAbajo() {
            const div = document.getElementById('mensajes');
            div.scrollTop = div.scrollHeight;
        }

        document.getElementById('busquedaChat').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.chat-item').forEach(item => {
                const nombre = item.getAttribute('data-nombre');
                item.style.display = nombre.includes(query) ? '' : 'none';
            });
        });

        const socket = io("http://127.0.0.1:3001");
        socket.on("mensaje-nuevo", function(data) {
            if (data.receptor_id == remitenteId && data.receptor_tipo == remitenteTipo) {
                cargarChats();
                cargarMensajes();
            }
        });

        cargarChats();
    </script>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const inputBusqueda = document.getElementById('buscadorContactos');
    const totalSpan = document.getElementById('totalContactos');
    const mensajeVacio = document.getElementById('mensaje-vacio');

    const encabezadoUsuarios = document.querySelector('h6.text-primary');
    const listaUsuarios = document.getElementById('lista-usuarios');

    const encabezadoProveedores = document.querySelector('h6.text-warning');
    const listaProveedores = document.getElementById('lista-proveedores');

    inputBusqueda.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        let total = 0;
        let usuariosVisibles = 0;
        let proveedoresVisibles = 0;

        document.querySelectorAll('.contacto').forEach(item => {
            const nombre = item.getAttribute('data-nombre');
            const visible = nombre.includes(query);

            item.style.display = visible ? '' : 'none';

            if (visible) {
                total++;
                if (item.dataset.tipo === 'U') usuariosVisibles++;
                if (item.dataset.tipo === 'P') proveedoresVisibles++;
            }
        });

        encabezadoUsuarios.style.display = usuariosVisibles > 0 ? '' : 'none';
        listaUsuarios.style.display = usuariosVisibles > 0 ? '' : 'none';

        encabezadoProveedores.style.display = proveedoresVisibles > 0 ? '' : 'none';
        listaProveedores.style.display = proveedoresVisibles > 0 ? '' : 'none';

        mensajeVacio.style.display = total === 0 ? 'block' : 'none';
        totalSpan.innerText = `Total: ${total} contacto${total !== 1 ? 's' : ''}`;
    });

    // Activar evento de click para cada contacto
    document.querySelectorAll('.contacto').forEach(c => {
        c.addEventListener('click', function () {
            contactoSeleccionado = this.dataset.id;
            tipoContacto = this.dataset.tipo;
            document.getElementById('nombre-contacto').innerText = this.innerText.trim();
            document.getElementById('introMensaje').style.display = 'none';
            document.getElementById('mensajes').style.display = 'flex';
            document.getElementById('btnVolver').classList.remove('d-none');
            document.getElementById('contenedorFormulario').style.display = 'block';

            // limpiar mensajes no leídos
            marcarComoLeido(contactoSeleccionado, tipoContacto);
            cargarMensajes();
            bootstrap.Modal.getInstance(document.getElementById('modalContactos')).hide();
        });
    });
});
</script>


@endsection
