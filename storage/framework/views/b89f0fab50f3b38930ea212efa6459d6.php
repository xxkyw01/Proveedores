<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitud de Cancelación de Cita</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f1f1;
            font-family: 'Segoe UI', sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
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
            color: #000000;
            text-align: left;
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
            margin: 0.5em 0;
        }

        .hero h2 {
            font-size: 20px;
            margin: 1em 0 0.5em;
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
            text-align: left;
            padding: 0.5em;
            border: 1px solid #ddd;
        }

        .detalle-cita th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .footer {
            background-color: #f6b88c1a;
            font-size: 14px;
            color: rgba(0, 0, 0, .6);
        }

        .footer h5 {
            color: #ee7826;
            margin: 0.5em 0;
        }
    </style>
</head>

<body>
    <table>
        <tr>
            <td class="header">
                <div>
                    <h6>
                        <strong>Materias Primas La Concepción</strong><br>
                        MPC831121U59
                    </h6>
                </div>
            </td>
        </tr>

        <tr>
            <td class="hero">
                <h1>Hola</h1>
                <h3>Se ha solicitado la <strong>Cancelación de una Cita</strong></h3>

                <h2>Detalles de la cita</h2>
                <div class="detalle-cita">
                    <table>
                        <tr>
                            <th>ID de la Cita</th>
                            <td><?php echo e($data['id']); ?></td>
                        </tr>
                        <tr>
                            <th>Proveedor</th>
                            <td><?php echo e($data['proveedor']); ?></td>
                        </tr>
                        <tr>
                            <th>Sucursal</th>
                            <td><?php echo e($data['sucursalNombre'] ?? 'No especificada'); ?></td>
                        </tr>
                        <tr>
                            <th>Fecha Programada</th>
                            <td><?php echo e(\Carbon\Carbon::parse($data['fechaCita'])->translatedFormat('l, d \\d\\e F \\d\\e Y')); ?>

                            </td>
                        </tr>
                        <tr>
                            <th>Hora</th>
                            <td><?php echo e(\Carbon\Carbon::parse($data['horaCita'])->format('h:i A')); ?></td>
                        </tr>
                        <tr>
                            <th>Transporte</th>
                            <td><?php echo e($data['transporte']); ?></td>
                        </tr>
                        <tr>
                            <th>Andén</th>
                            <td><?php echo e($data['anden']); ?></td>
                        </tr>
                        <tr>
                            <th>Motivo de Cancelación</th>
                            <td><?php echo e($data['motivo']); ?></td>
                        </tr>
                        <tr>
                            <th>Fecha de Solicitud</th>
                            <td><?php echo e($data['fechaSolicitud']); ?></td>
                        </tr>
                    </table>
                </div>

                <p style="margin-top: 1.5em;">Por favor, revisar esta solicitud de cancelación en el sistema.</p>
            </td>
        </tr>

        <tr>
            <td class="footer">
                <h5>Gracias por tu atención</h5>
                <hr>
                <p>Este mensaje fue generado automáticamente desde el sistema de proveedores</p>
            </td>
        </tr>
    </table>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/emails/solicitud_cancelacion.blade.php ENDPATH**/ ?>