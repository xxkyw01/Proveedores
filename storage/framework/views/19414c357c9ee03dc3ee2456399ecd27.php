<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Express</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f1f1f1;
            font-family: 'Segoe UI', sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .header,
        .footer {
            padding: 1.5em;
            text-align: center;
        }

        .header h6 {
            font-weight: 600;
            font-size: 14px;
            text-align: left;
            color: #000;
        }

        .hero {
            padding: 2em;
            text-align: center;
        }

        .hero h1 {
            color: #000;
            font-size: 24px;
            margin-bottom: 0.5em;
        }

        .hero h3 {
            color: rgba(0, 0, 0, .8);
            font-weight: 500;
            margin: .5em 0;
        }

        .hero h2 {
            font-size: 20px;
            margin: 1em 0 .5em;
            color: #009ca1;
        }

        .detalle-cita {
            text-align: left;
            margin-top: 1em;
        }

        .detalle-cita table {
            width: 100%;
            margin-top: 1em;
            border: 1px solid #ddd;
        }

        .detalle-cita th,
        .detalle-cita td {
            padding: .5em;
            border: 1px solid #ddd;
            text-align: left;
        }

        .detalle-cita th {
            background: #f5f5f5;
            font-weight: bold;
        }

        .footer {
            background: #f6b88c1a;
            font-size: 14px;
            color: rgba(0, 0, 0, .6);
        }

        .footer h5 {
            color: #ee7826;
            margin: .5em 0;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td class="header">
                <h6><strong>Materias Primas La Concepción</strong><br>MPC831121U59</h6>
            </td>
        </tr>
        <tr>
            <td class="hero">
                <h1>Hola</h1>
                <h3>Se ha registrado una <strong>Cita Express</strong></h3>

                <h2>Detalles de la cita</h2>
                <div class="detalle-cita">
                    <table>
                        <tr>
                            <th>Sucursal</th>
                            <td><?php echo e($detalles['sucursal']); ?></td>
                        </tr>
                        <tr>
                            <th>Tipo Entrega</th>
                            <td><?php echo e($detalles['tipo_entrega']); ?></td>
                        </tr>
                        <tr>
                            <th>Proveedor</th>
                            <td><?php echo e($detalles['proveedor']); ?></td>
                        </tr>
                        <tr>
                            <th>Fecha</th>
                            <td><?php echo e($detalles['fecha']); ?></td>
                        </tr>
                        <tr>
                            <th>Hora</th>
                            <td><?php echo e($detalles['hora']); ?></td>
                        </tr>
                        <tr>
                            <th>Descripción</th>
                            <td><?php echo e($detalles['descripcion']); ?></td>
                        </tr>
                    </table>
                </div>

                <?php if(!empty($detalles['evidencia'])): ?>
                    <h2 style="margin-top: 1em;">Evidencia</h2>
                    <ul style="list-style:none;padding:0;">
                        <li><a href="<?php echo e($detalles['evidencia']); ?>"
                                target="_blank"><?php echo e(basename($detalles['evidencia'])); ?></a></li>
                    </ul>
                <?php endif; ?>

                <p style="margin-top: 1.5em;">Por favor, revisa esta cita en el sistema.</p>
            </td>
        </tr>
        <tr>
            <td class="footer">
                <h5>Gracias por tu atención</h5>
                <hr>
                <p>Este mensaje fue generado automáticamente desde el sistema</p>
            </td>
        </tr>
    </table>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/emails/cita_express.blade.php ENDPATH**/ ?>