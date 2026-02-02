
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <title>Confirmación de Cita</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #f1f1f1;
      font-family: 'Work Sans', sans-serif;
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

    .header, .footer {
      padding: 1.5em;
      text-align: center;
    }

    .header {
      background-color: #ffffff;
      display: flex;
      justify-content: space-between;
      align-items: center;
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

    .hero h5 {
      color: rgba(0, 0, 0, .7);
      font-weight: 400;
      margin: 0.25em 0;
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

    .detalle-cita th, .detalle-cita td {
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
<?php
use Carbon\Carbon;
Carbon::setlocale('es_MX');
?>

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
        <h1>¡Hola!</h1>
        <h3>El proveedor <strong><?php echo e($proveedor); ?></strong> ha agendado una cita para entrega </h3>
        <h3>Por favor, puedes revisar los detalles completos de la reservación y confirmar la cita desde el sistema.</h3>
        <h2>Detalles de la cita</h2>
        <div class="detalle-cita">
          <table>
            <tr>
              <th>Fecha</th>
              <td><?php echo e(Carbon::parse($fecha)->translatedFormat('l d \d\e F \d\e Y')); ?></td>
            </tr>
            <tr>
              <th>Lugar</th>
              <td><?php echo e($sucursal); ?></td>
            </tr>
            <tr>
              <th>Datos de reservación</th>
              <td>
                  <ul style="margin: 0; padding-left: 1.2em;">
                      <?php $__currentLoopData = $horarios; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <li><?php echo e($item['detalle']); ?></li>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </ul>
              </td>
          </tr>
                      
          </table>
        </div>
        <p class="fw-normal" style="margin-top: 1.5em;">Recuerda que es importante confirmar o rechazar esta cita con anticipación para garantizar una correcta gestión.</p>
      </td>
    </tr>

    <tr>
      <td class="footer">
        <h5>Gracias por tu atención, saludos</h5>
        <hr>
        <p>Si tiene dudas, puede ponerse en contacto.</p>
      </td>
    </tr>
  </table>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/emails/cita_pendiente.blade.php ENDPATH**/ ?>