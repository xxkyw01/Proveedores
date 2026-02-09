<!DOCTYPE html>
<html class="no-js h-100" lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<meta http-equiv="Cache-Control" content="no-store" />
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php echo $__env->make('includes.scripts.SweetAlert2', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<head>
    <title>Login Proveedor</title>
    <?php echo $__env->make('includes.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script>
        function onSubmit(token) {
            document.getElementById("login-form").submit();
        }
    </script>
</head>

<body class="bg-cream d-flex flex-column h-100">

    <main class="flex-shrink-0">

        <div class="loader-in" id="loader">
            <img src="<?php echo e(asset('assets/img/splash.png')); ?>" alt="Materias Primas La Concepción">
        </div>

        <div class="container">
            <div class="d-flex justify-content-center align-items-center vh-90">
                <div class="row">
                    <div class="col-lg-12 col-ms-10">
                        <div class="text-center">
                            <a class="d-block mx-auto">
                                <img class="img-fluid" src="<?php echo e(asset('assets/img/login.png')); ?>" alt="La Concha">
                            </a>
                        </div>

                        <div class="card border-orange full-shadow rounded-3 m-1 p-1">
                            <h3 class="text-center text-orange fw-bold mb-3 mt-4">Ingreso Proveedor</h3>

                            <div class="card-body">
                                <?php
                                    $cookiePass = Request::cookie('laravel_key');
                                    $cookieUser = Request::cookie('laravel_log');
                                    $cookieCheck = Request::cookie('laravel_check');
                                    $Pass = $cookiePass ?? '';
                                    $User = $cookieUser ?? old('user');
                                    $Checked = $cookieCheck ?? '';
                                ?>

                                <?php if($errors->has('errorMsg')): ?>
                                    <div class="alert alert-danger"><?php echo e($errors->first('errorMsg')); ?></div>
                                    <script>
                                        Swal.fire({
                                            icon: 'error',
                                            title: "<?php echo e($errors->first('errorMsg')); ?>",
                                            toast: true
                                        });
                                        Android.showToast("<?php echo e($errors->first('errorMsg')); ?>");
                                    </script>
                                <?php endif; ?>

                                <?php if(session('errors') || $errors->has('errorMsg')): ?>
                                    <?php
                                        $User = '';
                                        $Pass = '';
                                        $Checked = '';
                                    ?>
                                <?php endif; ?>


                                <form id="login-form" method="POST" action="<?php echo e(route('proveedor_login_post')); ?>">

                                    <?php echo csrf_field(); ?>

                                    <div id="divLoged" style="display: block;">
                                        <h1 class="text-center">Hola de nuevo <br> <?php echo e($User); ?></h1>
                                        <h6 class="text-center">
                                            <a class="no-link text-orange" href="#" id="myLink">Cambiar de
                                                usuario</a>
                                        </h6>
                                    </div>

                                    <div id="divLogin" style="display: none;">
                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="user-input">
                                                <i class="fas fa-user"></i>
                                            </span>
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-sm"
                                                    id="form-user" name="user" placeholder="Usuario"
                                                    value="<?php echo e($User); ?>" required autofocus autocomplete="false">
                                                <label for="user-input">Usuario proveedor:</label>
                                            </div>
                                        </div>

                                        <div class="input-group mb-1">
                                            <span class="input-group-text" id="pass-input">
                                                <i class="fas fa-key"></i>
                                            </span>
                                            <div class="form-floating">
                                                <input type="password" class="form-control" id="form-pass"
                                                    name="password" placeholder="Contraseña" value="<?php echo e($Pass); ?>"
                                                    required autocomplete="false">
                                                <label for="password">Contraseña:</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!----
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" name="record" id="form-record" <?php echo e($Checked); ?>>
                                        <label class="form-check-label" for="form-record">Recordarme</label>
                                    </div>--->

                                    

                                    <div class="d-grid gap-2">
                                        <button class="g-recaptcha btn btn-primary"
                                            data-sitekey="<?php echo e(config('services.recaptcha.site_key')); ?>"
                                            data-callback="onSubmit" data-action="login">INICIAR SESSION <i
                                                class="fas fa-sign-in-alt"></i>
                                        </button>
                                    </div>
                                    
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer mt-auto py-1 bg-footer fixed-bottom"></footer>
    </main>
</body>

</html>



<?php if(session('logoutMsg')): ?>
    < script>
        Swal.fire({
        icon: 'info',
        title: "<?php echo e(session('logoutMsg')); ?>",
        toast: true
        });
        Android.showToast("<?php echo e(session('logoutMsg')); ?>");
        </script>
<?php endif; ?>

<script>
    window.history.forward();

    function onSubmit(token) {
        document.getElementById("login-form").submit();
    }

    window.addEventListener("load", function() {
        setTimeout(() => document.getElementById("loader").classList.toggle("loader-out"), 5000);
    });

    $(document).ready(function() {
        const User = "<?php echo e($User); ?>";
        const Pass = "<?php echo e($Pass); ?>";
        const Checked = "<?php echo e($Checked); ?>";
        const divLogin = document.getElementById('divLogin');
        const divLoged = document.getElementById('divLoged');

        if (User || Pass || Checked) {
            divLoged.style.display = 'block';
            divLogin.style.display = 'none';
        } else {
            divLogin.style.display = 'block';
            divLoged.style.display = 'none';
        }
    });

    document.getElementById("myLink").addEventListener("click", function(event) {
        event.preventDefault();

        const divLogin = document.getElementById('divLogin');
        const divLoged = document.getElementById('divLoged');

        $('user').val('');
        $('password').val('');
        $('record').prop('checked', false);

        if (divLogin.style.display === 'none') {
            divLogin.style.display = 'block';
            divLoged.style.display = 'none';
        } else {
            divLoged.style.display = 'block';
            divLogin.style.display = 'none';
        }

    });
</script>

<?php /**PATH C:\Users\ygonzalez\Synology\Home\Escritorio\Proveedores\resources\views/auth/login.blade.php ENDPATH**/ ?>