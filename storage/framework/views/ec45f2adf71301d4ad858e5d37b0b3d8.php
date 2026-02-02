
<?php $__env->startSection('title', 'Citas Express'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('includes.scripts.SweetAlert2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.flatpickr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('includes.scripts.bootstrap', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if (isset($component)) { $__componentOriginal2880b66d47486b4bfeaf519598a469d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2880b66d47486b4bfeaf519598a469d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('sidebar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $attributes = $__attributesOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__attributesOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2880b66d47486b4bfeaf519598a469d6)): ?>
<?php $component = $__componentOriginal2880b66d47486b4bfeaf519598a469d6; ?>
<?php unset($__componentOriginal2880b66d47486b4bfeaf519598a469d6); ?>
<?php endif; ?>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <!-- Enlace al CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/rol/almacen/express.css')); ?>">

    <div class="container-fluid con-sidebar">

        <div class="container">
            <h4 class="titulo-paso">
                <i class="fas fa-shipping-fast text-orange"></i> Citas Express Recepción Inmediata
            </h4>

            <!-- FORMULARIO COMPLETO -->
            <form id="form-cita-express">
                <div class="mb-3">
                    <label for="ciudad" class="form-label">Ciudad</label>
                    <select id="ciudad" name="ciudad" class="form-select custom-select" required>
                        <option value="">Seleccione...</option>
                        <?php $__currentLoopData = $ciudades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>"><?php echo e($c->nombre); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="sucursal" class="form-label">CDI</label>
                    <select id="sucursal" name="sucursal_id" class="form-select custom-select" required>
                        <option value="">Seleccione...</option>
                    </select>
                </div>

                <h5 class="titulo-paso">
                    <i class="fas fa-box text-orange"></i> Detalles de la Entrega
                </h5>

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de entrega</label>
                    <select id="tipo" name="qbox" class="form-select custom-select" required>
                        <option value="Paquetería">Paquetería</option>
                        <option value="Uber">Uber</option>
                        <option value="Didi">Didi</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="proveedor" class="form-label">Proveedor (opcional)</label>
                    <input type="text" id="proveedor" name="proveedor_id" class="form-control custom-input"
                        placeholder="Código del proveedor (si aplica)">
                </div>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" id="fecha" name="fecha" class="form-control custom-input" required>
                </div>

                <div class="mb-3">
                    <label for="hora" class="form-label">Hora (opcional)</label>
                    <input type="time" id="hora" name="hora" class="form-control custom-input">
                    <small class="text-muted">
                        Si no selecciona, se usará 17:00 (5:00 PM)
                    </small>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción o contenido</label>
                    <textarea id="descripcion" name="descripcion" class="form-control custom-input" minlength="50" required
                        placeholder="Descripción del paquete o contenido (mínimo 50 caracteres) es recomendable tambien mencionar la orden de compra"></textarea>
                </div>

                <div class="mb-3">
                    <label for="archivo" class="form-label">Adjunto de archivo</label>
                    <input type="file" id="archivo" name="evidencias" class="form-control custom-input">
                </div>

                <button type="submit" class="btn btn-primary w-100">Registrar entrega express</button>
            </form>
        </div>

    </div> 

    <script>
        document.getElementById('ciudad').addEventListener('change', function() {
            const ciudadId = this.value;

            const sucursalSelect = document.getElementById('sucursal');
            sucursalSelect.innerHTML = "<option value=''>Seleccione...</option>";

            if (!ciudadId) return;

            fetch(`/almacen/cita-express/sucursales/${ciudadId}`)
                .then(res => {
                    if (!res.ok) throw new Error("No se pudieron cargar las sucursales");
                    return res.json();
                })
                .then(data => {
                    data.forEach(s => {
                        sucursalSelect.innerHTML += `<option value="${s.id}">${s.nombre}</option>`;
                    });
                })
                .catch(err => {
                    console.error("Error cargando sucursales:", err);
                    Swal.fire('Error', 'No se pudieron cargar las sucursales', 'error');
                });
        });

        document.getElementById('form-cita-express').addEventListener('submit', function(e) {
            e.preventDefault();

            const sucursalSelect = document.getElementById('sucursal');
            if (!sucursalSelect.value) {
                Swal.fire('Error', 'Seleccione una sucursal', 'error');
                return;
            }

            const formData = new FormData(this);

            console.log("FormData enviado:");
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            fetch('/almacen/cita-express/registrar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(async (res) => {
                    const data = await res.json().catch(() => ({}));
                    if (!res.ok) {
                        const msg = data.message || `Error ${res.status}`;
                        if (res.status === 401) Swal.fire('Sesión', msg ||
                            'No autenticado o sesión expirada', 'warning');
                        else if (res.status === 422) Swal.fire('Validación', (data.errors && JSON.stringify(
                            data.errors)) || msg, 'warning');
                        else Swal.fire('Error', msg, 'error');
                        throw new Error(msg);
                    }
                    return data;
                })
                .then((data) => {
                    if (data.success) {
                        Swal.fire('¡Listo!', data.message, 'success');
                        document.getElementById('form-cita-express').reset();
                        document.getElementById('sucursal').innerHTML =
                            "<option value=''>Seleccione...</option>";
                    } else {
                        Swal.fire('Error', data.message || 'Ocurrió un problema al guardar la cita', 'error');
                    }
                })
                .catch((err) => {
                    console.error('Error en fetch:', err);
                });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.movil', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/pages/almacen/CitaExpress.blade.php ENDPATH**/ ?>