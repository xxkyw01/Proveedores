<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Recibo de mercancía</title>
    <style>
        :root {
            --brand: #ee7826;
            --muted: #666;
            --line: #e5e7eb;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #111;
            margin: 0;
            padding: 24px;
            background: #f7f7f7;
        }

        .paper {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 18px 18px 12px;
            max-width: 900px;
            margin: 0 auto;
        }

        header {
            display: flex;
            gap: 12px;
            align-items: center;
            border-bottom: 2px solid var(--brand);
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        header img {
            height: 56px;
        }

        header .title {
            flex: 1;
        }

        header h1 {
            margin: 0;
            font-size: 20px;
            color: var(--brand);
        }

        header .meta {
            font-size: 12px;
            color: var(--muted);
            margin-top: 4px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 12px 0 14px;
        }

        .box {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 10px;
        }

        .box h3 {
            margin: 0 0 8px;
            font-size: 13px;
            color: #111;
        }

        .kv {
            font-size: 13px;
            line-height: 1.45;
        }

        .kv b {
            color: #111;
        }

        .pill {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #fff3e6;
            border: 1px solid #ffd6b0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            text-align: left;
            padding: 10px 8px;
            background: #fff3e6;
            border: 1px solid #ffd6b0;
        }

        tbody td {
            padding: 8px;
            border: 1px solid var(--line);
            vertical-align: top;
        }

        .c-qty {
            text-align: right;
            font-weight: 700;
            width: 110px;
        }

        .c-um {
            width: 90px;
            text-align: center;
            color: var(--muted);
        }

        .c-code {
            width: 120px;
            font-weight: 700;
        }

        tfoot td {
            padding: 10px 8px;
            border: 1px solid var(--line);
        }

        .totalLbl {
            text-align: right;
            font-weight: 700;
        }

        .totalVal {
            text-align: right;
            font-weight: 900;
            font-size: 15px;
        }

        .notes {
            margin-top: 10px;
            border: 1px dashed #ffd6b0;
            background: #fffaf5;
            padding: 10px;
            border-radius: 10px;
            font-size: 12.5px;
            color: #333;
        }

        .sign {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-top: 18px;
        }

        .line {
            border-top: 1px solid #bbb;
            padding-top: 6px;
            font-size: 12px;
            color: #333;
            text-align: center;
        }

        footer {
            margin-top: 14px;
            text-align: center;
            font-size: 12px;
            color: var(--muted);
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .paper {
                border: none;
                border-radius: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <div class="paper">
        <header>
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            <div class="title">
                <h1>Recibo de Mercancía</h1>
                <div class="meta">Generado {{ now()->format('d/m/Y') }} {{ now()->format('H:i') }} · Capturó: <b>{{ auth()->user()->name ?? '' }}</b></div>
            </div>
            <div style="text-align:right">
                <div class="pill">Entrega de Mercancia: <b>{{ $pdn['DocNum'] ?? 'PENDIENTE' }}</b></div><br />
                <div class="pill">Orden de Compra: <b>{{ $pdn['NumAtCard'] ?? ($pdn['BaseRef'] ?? '') }}</b></div>
            </div>
        </header>

        <div class="grid">
            <div class="box">
                <h3>Proveedor</h3>
                <div class="kv">
                    <b>{{ $pdn['CardName'] ?? $pdn['ProveedorNombre'] ?? 'N/A' }}</b><br />
                    <span style="color:var(--muted)">Código:</span> {{ $pdn['CardCode'] ?? $pdn['ProveedorCodigo'] ?? 'N/A' }}<br />
                    <span style="color:var(--muted)">Folio proveedor:</span> <b>{{ $pdn['NumAtCard'] ?? '' }}</b>
                </div>
            </div>

            <div class="box">
                <h3>Datos de cita / recepción</h3>
                <div class="kv">
                    <span style="color:var(--muted)">Sucursal:</span> <b>{{ $pdn['BranchName'] ?? $pdn['SucursalNombre'] ?? 'N/A' }}</b><br />
                    <span style="color:var(--muted)">Fecha:</span> {{ isset($pdn['DocDate']) ? \Carbon\Carbon::parse($pdn['DocDate'])->format('d/m/Y') : '' }}<br />
                    <span style="color:var(--muted)">Hora:</span> {{ isset($pdn['DocTime']) ? $pdn['DocTime'] : '' }}<br />
                    <span style="color:var(--muted)">Transporte:</span> {{ $pdn['Transport'] ?? $pdn['transporte'] ?? 'N/A' }}<br />
                    <span style="color:var(--muted)">Lugar:</span> {{ $pdn['Location'] ?? $pdn['Lugar'] ?? 'N/A' }}
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Artículo</th>
                    <th style="text-align:right">Cantidad</th>
                    <th style="text-align:center">UM</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($lines) && is_array($lines) && count($lines) > 0)
                    @foreach($lines as $l)
                        <tr>
                            <td class="c-code">{{ $l['ItemCode'] ?? $l['Codigo'] ?? $l['U_Articulo'] ?? '' }}</td>
                            @php
                                $desc = $l['Dscription'] ?? $l['ItemName'] ?? $l['Description'] ?? $l['Articulo'] ?? $l['ItemNameL'] ?? $l['LineDescription'] ?? $l['ItemNameLocal'] ?? $l['U_Descripcion'] ?? $l['ItemDesc'] ?? '';
                            @endphp
                            <td>{{ $desc }}</td>
                            <td class="c-qty">{{ number_format(floatval($l['Quantity'] ?? $l['QuantityOrdered'] ?? 0), 2) }}</td>
                            <td class="c-um">{{ $l['UnitMsr'] ?? $l['UomCode'] ?? $l['U_Medida'] ?? '' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">Sin líneas.</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="totalLbl">Total recibido</td>
                    <td class="totalVal">{{ number_format(floatval($pdn['DocTotal'] ?? $pdn['Total'] ?? $summary['totalQty'] ?? 0), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        @if(!empty($pdn['Comments'] ?? $pdn['Comentarios'] ?? ''))
            <div class="notes"><b>Comentarios:</b><br />{{ $pdn['Comments'] ?? $pdn['Comentarios'] ?? '' }}</div>
        @endif

        <div class="sign">
            <div class="line">Entrega (Proveedor)</div>
            <div class="line">Recibe (Almacén)</div>
        </div>

        <footer>Reporte generado automáticamente · {{ now()->format('d/m/Y') }}</footer>
    </div>

    <script>
        window.onload = function() {
            window.focus();
            setTimeout(function() { window.print(); }, 250);
        };
    </script>
</body>
</html>
