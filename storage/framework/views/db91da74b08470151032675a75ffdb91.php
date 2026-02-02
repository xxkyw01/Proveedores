<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitud de Actualización</title>
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
      text-align: left;
    }
    .hero h1 {
      color: #ee7826;
      font-size: 22px;
      margin-bottom: 1em;
    }
    .hero p {
      font-size: 16px;
      margin: 0.5em 0;
      color: #333;
    }
    ul {
      margin: 1em 0;
      padding-left: 1.2em;
    }
    ul li {
      font-size: 15px;
      color: #222;
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
    .footer hr {
      margin: 1em 0;
      border: none;
      border-top: 2px solid #ee7826;
      width: 60%;
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
        <div>  
        </div>
      </td>
    </tr>

    <tr>
      <td class="hero">
        <h1>Solicitud de Actualización de Datos</h1>
        <p><strong>Proveedor:</strong> <?php echo e($proveedor->Nombre_Proveedor); ?></p>
        <p><strong>Código:</strong> <?php echo e($proveedor->Codigo_Proveedor ?? 'N/A'); ?></p>
        <p><strong>Correo Registrado:</strong> <?php echo e($proveedor->Correo_Electronico ?? 'No registrado'); ?></p>

        <p><strong>Campos solicitados para actualizar:</strong></p>
        <ul>
          <?php $__currentLoopData = $campos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($campo); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

        <p style="margin-top: 1.5em;">Este correo fue enviado automáticamente desde el sistema de citas.</p>
      </td>
    </tr>

    <tr>
      <td class="footer">
        <h5>Gracias por tu atención</h5>
        <p>Este mensaje fue generado automáticamente desde el sistema</p>
      </td>
    </tr>
  </table>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Proveedores\resources\views/emails/solicitud_actualizacion.blade.php ENDPATH**/ ?>