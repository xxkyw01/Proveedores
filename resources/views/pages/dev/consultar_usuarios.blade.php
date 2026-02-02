@extends('layouts.movil')
@section('title', 'Usuarios del Sistema')
@section('content')
    @include('includes.scripts.Selectize')
    @include('includes.scripts.SweetAlert2')
    @include('includes.scripts.Datatables')


    <x-sidebar />

    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">

            <div class="container mt-4">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $e)
                            <p>{{ $e }}</p>
                        @endforeach
                    </div>
                @endif

                <h4 class="section-title mt-4 mb-3 text-white bg-orange p-2 rounded">
                    <i class="fas fa-truck me-2"></i> Proveedores
                </h4>
                <table id="tablaProveedores" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>CardCode</th>
                            <th>Activo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proveedores as $p)
                            <tr>
                                <td>{{ $p->id }}</td>
                                <td>{{ $p->username }}</td>
                                <td>{{ $p->CardCode }}</td>
                                <td>{{ $p->activo }}</td>
                                <td>
                                    <form method="POST" action="{{ route('dev.usuario.password') }}" class="form-password">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $p->id }}">
                                        <input type="hidden" name="rol" value="proveedor">
                                        <div class="input-group">
                                            <input type="password" name="password"
                                                class="form-control form-control-sm password-input"
                                                placeholder="Nueva contraseña..." required minlength="8">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-confirmar-password">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <h4 class="section-title mt-5 mb-3 text-white bg-dark p-2 rounded">
                    <i class="fas fa-users-cog me-2"></i> Usuarios Internos
                </h4>
                <table id="tablaInternos" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Nombre</th>
                            <th>Activo</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($internos as $u)
                            <tr>
                                <td>{{ $u->IdUsuario }}</td>
                                <td>{{ $u->Codigo }}</td>
                                <td>{{ $u->IdRol }}</td>
                                <td>{{ $u->Nombre }}</td>
                                <td>{{ $u->Activo }}</td>
                                <td>
                                    <form method="POST" action="{{ route('dev.usuario.password') }}"
                                        class="form-password">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $u->IdUsuario }}">
                                        <input type="hidden" name="rol" value="interno">
                                        <div class="input-group">
                                            <input type="password" name="password"
                                                class="form-control form-control-sm password-input"
                                                placeholder="Nueva contraseña..." required minlength="8">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning btn-confirmar-password">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div> {{-- Cierra row --}}
    </div> {{-- Cierra container-fluid --}}



    <script>
        $(document).ready(function() {
            $('#tablaProveedores').DataTable({
                responsive: true,
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    emptyTable: "No hay datos disponibles"
                }
            });

            $('#tablaInternos').DataTable({
                responsive: true,
                language: {
                    search: "Buscar:",
                    lengthMenu: "Mostrar _MENU_ registros",
                    info: "Mostrando _START_ a _END_ de _TOTAL_",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    },
                    emptyTable: "No hay datos disponibles"
                }
            });

            const botones = document.querySelectorAll('.btn-confirmar-password');
            botones.forEach(btn => {
                btn.addEventListener('click', function() {
                    const form = this.closest('form');
                    const password = form.querySelector('.password-input').value;

                    if (password.length < 8) {
                        Swal.fire('Error', 'La contraseña debe tener al menos 8 caracteres.',
                            'error');
                        return;
                    }

                    Swal.fire({
                        title: '¿Estás segura?',
                        text: 'Se actualizará la contraseña de este usuario.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ee7826',
                        cancelButtonColor: '#aaa',
                        confirmButtonText: 'Sí, actualizar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>

@endsection
