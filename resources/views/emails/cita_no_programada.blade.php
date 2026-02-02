<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cita No Programada</title>

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
        <h3>Se ha registrado una <strong>Cita No Programada</strong></h3>

        <h2>Detalles de la cita</h2>
        <div class="detalle-cita">
          <table>
            <tr>
              <th>Proveedor</th>
              <td>{{ $detalles['proveedor'] }}</td>
            </tr>
            <tr>
              <th>Sucursal</th>
              <td>{{ $detalles['sucursal'] }}</td>
            </tr>
            <tr>
              <th>Fecha</th>
              <td>{{ $detalles['fecha'] }}</td>
            </tr>
            <tr>
              <th>Tipo de Evento</th>
              <td>No Programada</td>
            </tr>
            <tr>
              <th>Motivo</th>
              <td>{{ $detalles['motivo'] }}</td>
            </tr>
          </table>
        </div>

        <h2 style="margin-top: 1em;">Vehículos</h2>
        <div class="detalle-cita">
          <table>
            <thead>
              <tr>
                <th>Andén</th>
                <th>Hora</th>
                <th>Transporte</th>
              </tr>
            </thead>
            <tbody>
              @foreach($detalles['vehiculos'] as $v)
              <tr>
                <td>{{ $v['anden_id'] }}</td>
                <td>{{ $v['hora'] }}</td>
                <td>{{ $v['transporte_id'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        @if(!empty($detalles['evidencias']))
        <h2 style="margin-top: 1em;">Evidencias</h2>
        <ul style="list-style:none;padding:0;">
          @foreach($detalles['evidencias'] as $e)
            <li><a href="{{ $e }}" target="_blank">{{ basename($e) }}</a></li>
          @endforeach
        </ul>
        @endif

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
