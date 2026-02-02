@extends('layouts.movil')
@section('title', 'Crear Usuario Dev')
@section('content')

    <x-sidebar />

    <div class="container-fluid con-sidebar">
        <div class="row justify-content-center">

            <div class="container mt-5">
                <h3>Crear Usuario con Contraseña Segura</h3>
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('dev.crear') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Usuario:</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña:</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol:</label>
                        <select name="rol" class="form-select" required>
                            <option value="proveedor">Proveedor</option>
                            <option value="admin">Administrador</option>
                            <option value="almacen">Almacén</option>
                            <option value="compras">Compras</option>
                            <option value="mejora">Mejora Continua</option>
                            <option value="dev">Desarrollador</option>
                        </select>
                    </div>

                    <div class="mb-3" id="campoCardCode" style="display: none;">
                        <label class="form-label">CardCode (para proveedor):</label>
                        <input type="text" name="cardcode" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-success">Crear Usuario</button>
                </form>
            </div>

        </div> {{-- Cierra row --}}
    </div> {{-- Cierra container-fluid --}}


    <script>
        document.querySelector('select[name="rol"]').addEventListener('change', function() {
            document.getElementById('campoCardCode').style.display =
                this.value === 'proveedor' ? 'block' : 'none';
        });
    </script>
@endsection
