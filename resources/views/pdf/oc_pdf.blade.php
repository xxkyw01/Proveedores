<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orden de Compra - {{ $po['DocNum'] ?? 'OC' }}</title>
    <style>
        /* Base */
        @page { margin: 20mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #2b2b2b; font-size:12px; }
        .container { width:100%; }

        /* Header */
        .header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
        .logo { width:140px; }
        .company { text-align:left; }
        .company .name { color:#ee7826; font-weight:800; font-size:20px; }
        .doc { text-align:right; }
        .doc .title { background:#ee7826; color:#fff; display:inline-block; padding:8px 14px; border-radius:6px; font-weight:800; }
        .meta { margin-top:6px; color:#6c757d; font-size:11px; }

        /* Info blocks */
        .info-row { display:flex; gap:12px; margin-top:16px; }
        .info { flex:1; border:1px solid #f1e7df; padding:12px; border-radius:8px; background:#fffdf8; }
        .info .label { font-size:11px; color:#6c757d; margin-bottom:6px; font-weight:700; }
        .info .value { font-weight:700; color:#222; }

        /* Table */
        .lines { margin-top:14px; width:100%; border-collapse:collapse; border:1px solid #eef3ff; }
        .lines th, .lines td { padding:8px 10px; border-bottom:1px solid #f1f4fb; font-size:12px; }
        .lines thead th { background:#fff7f0; color:#223; text-align:left; font-weight:800; }
        .lines tbody tr:nth-child(even) { background:#fffaf6; }
        .right { text-align:right; }
        .center { text-align:center; }

        /* Totals */
        .totals { display:flex; justify-content:flex-end; margin-top:12px; gap:16px; }
        .totals .box { width:260px; border-radius:6px; overflow:hidden; border:1px solid #e6eefc; }
        .totals .row { display:flex; justify-content:space-between; padding:8px 12px; background:#fff; }
        .totals .row.total { background:#0a58ca; color:#fff; font-weight:700; }

        /* Footer  */
        .footer { margin-top:18px; color:#6c757d; font-size:11px; border-top:1px solid #eef3ff; padding-top:8px; }

        /* Small helpers          */
        .small { font-size:11px; color:#6c757d; }

    </style>
</head>
<body>
    <div class="container">
        <?php
            use Carbon\Carbon;

            function uomLabel($code) {
                if (!$code) return '-';
                $c = strtoupper(trim((string)$code));
                $map = [
                    'BOX' => 'Caja', 'CAJA' => 'Caja', 'CTN' => 'Caja',
                    'BULTO' => 'Bulto', 'BLT' => 'Bulto',
                    'PZA' => 'Pieza', 'PZ' => 'Pieza', 'PC' => 'Pieza', 'EA' => 'Pieza', 'PIEZA' => 'Pieza',
                    'SET' => 'Set', 'ROL' => 'Rollo'
                ];
                foreach ($map as $k => $v) {
                    if (strpos($c, $k) !== false) return $v;
                }
                return ucfirst(strtolower($code));
            }

                $provContact = $po['CardContact'] ?? $po['ContactPerson'] ?? $po['ContactName'] ?? ($po['Contacto'] ?? null);
                if (empty($provContact) && !empty($po['CntctCode'])) {
                    $cnt = $po['CntctCode'];
                    $candidates = $po['Contacts'] ?? $po['BPContacts'] ?? $po['ContactEmployees'] ?? null;
                    if (is_array($candidates)) {
                        foreach ($candidates as $c) {
                            if (!is_array($c) && !is_object($c)) continue;
                            $obj = is_object($c) ? (array)$c : $c;
                            if ((isset($obj['CntctCode']) && (string)$obj['CntctCode'] === (string)$cnt)
                                || (isset($obj['InternalCode']) && (string)$obj['InternalCode'] === (string)$cnt)
                                || (isset($obj['ContactCode']) && (string)$obj['ContactCode'] === (string)$cnt)) {
                                $provContact = $obj['Name'] ?? $obj['FullName'] ?? $obj['ContactName'] ?? ('Contacto ' . $cnt);
                                break;
                            }
                        }
                    }
                    if (empty($provContact)) {
                        $provContact = 'Contacto ID: ' . $cnt;
                    }
                }
            $provAddress = $po['CardAddress'] ?? $po['Address'] ?? $po['CardAddress1'] ?? $po['ShipToAddress'] ?? ($po['Address2'] ?? null);
            $shipTo = $po['ShipTo'] ?? $po['ShipToAddress'] ?? $po['Address2'] ?? $po['U_ShipTo'] ?? $po['ShippingAddress'] ?? null;
            $docDate = '-';
            if (!empty($po['DocDate'])) {
                try { $docDate = Carbon::parse($po['DocDate'])->format('d/m/Y'); } catch (Exception $e) { $docDate = $po['DocDate']; }
            }
            $dueDate = '-';
            if (!empty($po['DocDueDate'] ?? $po['TaxDate'])) {
                try { $dueDate = Carbon::parse($po['DocDueDate'] ?? $po['TaxDate'])->format('d/m/Y'); } catch (Exception $e) { $dueDate = $po['DocDueDate'] ?? $po['TaxDate']; }
            }

            $rawStatus = $po['DocumentStatus'] ?? $po['DocumentStatusName'] ?? $po['Status'] ?? ($po['U_DocStatus'] ?? '');
            $statusLabel = '-';
            if ($rawStatus !== null && $rawStatus !== '') {
                $rs = strtolower((string)$rawStatus);
                if (strpos($rs, 'bost_open') !== false || strpos($rs, 'open') !== false || $rs === 'o') $statusLabel = 'Abierto';
                else if (strpos($rs, 'bost_closed') !== false || strpos($rs, 'closed') !== false || $rs === 'c') $statusLabel = 'Cerrado';
                else $statusLabel = ucfirst($rawStatus);
            }

            $computedSubtotal = 0.0;
            if (!empty($lines) && is_array($lines)) {
                foreach ($lines as $ln) {
                    $qty = floatval($ln['Quantity'] ?? $ln['QuantityOrdered'] ?? 0);
                    $price = floatval($ln['Price'] ?? $ln['UnitPrice'] ?? $ln['PriceBefDi'] ?? 0);
                    $lineTotal = 0.0;
                    if (isset($ln['LineTotal']) && $ln['LineTotal'] !== null) {
                        $lineTotal = floatval($ln['LineTotal']);
                    } else {
                        $lineTotal = $qty * $price;
                    }
                    $computedSubtotal += $lineTotal;
                }
            }
            $subtotal = ($po['DocSubTotal'] ?? $po['SubTotal'] ?? null);
            if (empty($subtotal) && $computedSubtotal > 0) $subtotal = $computedSubtotal;

            $discount = $po['DiscSum'] ?? $po['Discount'] ?? 0;
            $tax = $po['VatSum'] ?? $po['TaxSum'] ?? $po['VatSumFc'] ?? 0;
            $docTotal = $po['DocTotal'] ?? $po['Total'] ?? array_sum(array_map(function($l){ return floatval($l['LineTotal'] ?? ((floatval($l['Quantity'] ?? 0) * floatval($l['Price'] ?? 0)))); }, is_array($lines)?$lines:[]));

            function pickUomFromLine($ln) {
                if (!$ln || !is_array($ln)) return null;
                $cands = [];
                // posibles ubicaciones del uom en SAP/variantes
                if (isset($ln['Por1']) && is_array($ln['Por1'])) {
                    if (!empty($ln['Por1']['UomCode'])) $cands[] = $ln['Por1']['UomCode'];
                    if (!empty($ln['Por1']['Uom'])) $cands[] = $ln['Por1']['Uom'];
                }
                foreach (['Por1UomCode','Por1Uom','UomCode','Uom','UnitMsr','UomCodePor1'] as $k) {
                    if (!empty($ln[$k])) $cands[] = $ln[$k];
                }
                // también campos comunes
                if (!empty($ln['UomCode'])) $cands[] = $ln['UomCode'];
                if (!empty($ln['UnitMsr'])) $cands[] = $ln['UnitMsr'];
                foreach ($cands as $v) {
                    if ($v === null) continue;
                    $s = trim((string)$v);
                    if ($s !== '') return $s;
                }
                return null;
            }
        ?>

        <div class="header">
            <div class="company">
                <img src="{{ public_path('assets/img/logo.png') }}" class="logo" alt="logo">
                <div class="name">{{ config('app.name', 'Empresa') }}</div>
                <div class="small">{{ config('app.address', '') }}</div>
            </div>

            <div class="doc">
                <div class="title">Orden de Compra</div>
                <div class="meta">Folio: <strong>{{ $po['DocNum'] ?? '-' }}</strong></div>
                <div class="meta">Fecha: <strong>{{ $docDate }}</strong></div>
                <div class="meta">Usuario: <strong>{{ auth()->user()->name ?? 'Proveedor' }}</strong></div>
            </div>
        </div>

        <div class="info-row">
            <div class="info">
                <div class="label">Código proveedor</div>
                <div class="value">{{ $po['CardCode'] ?? '-' }}</div>
                <div class="label">Nombre proveedor</div>
                <div class="value">{{ $po['CardName'] ?? '-' }}</div>
                <div class="small">Persona de contacto: {{ $provContact ?? '-' }}</div>
                <div class="small">Dirección proveedor: {{ $provAddress ?? '-' }}</div>
            </div>

            <div class="info">
                <div class="label">Destino (Almacén)</div>
                <div class="value">{{ $shipTo ?? ($po['ShipTo'] ?? '-') }}</div>
                <div class="label">Serie - Documento</div>
                <div class="value">{{ ($po['Series'] ?? '') . ' - ' . ($po['DocNum'] ?? '') }}</div>
                <div class="small">Estado documento: {{ $statusLabel }}</div>
                <div class="small">Fecha contabilización: {{ $docDate }}</div>
                <div class="small">Fecha de entrega: {{ $dueDate }}</div>
                <div class="small">Fecha de documento: {{ $docDate }}</div>
            </div>
        </div>

        <table class="lines">
            <thead>
                <tr>
                    <th style="width:36px" class="center">#</th>
                    <th style="width:100px">Código</th>
                    <th>Descripción</th>
                    <th style="width:70px">Almacén</th>
                    <th style="width:70px" class="center">U.M.</th>
                    <th style="width:90px" class="right">Cantidad</th>
                    <th style="width:100px" class="right">Precio</th>
                    <th style="width:110px" class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lines ?? [] as $i => $ln)
                    <tr>
                        <td class="center">{{ $ln['LineNum'] ?? $i + 1 }}</td>
                        <td>{{ $ln['ItemCode'] ?? '-' }}</td>
                        <td>{{ $ln['ItemDescription'] ?? '-' }}</td>
                        <td>{{ $ln['WarehouseCode'] ?? ($ln['Warehouse'] ?? '-') }}</td>
                        <td class="center">{{ uomLabel(pickUomFromLine($ln)) }}</td>
                        <td class="right">{{ number_format($ln['Quantity'] ?? $ln['QuantityOrdered'] ?? 0, 2) }}</td>
                        <td class="right">{{ isset($ln['Price']) ? number_format($ln['Price'], 2) : (isset($ln['UnitPrice']) ? number_format($ln['UnitPrice'],2) : '-') }}</td>
                        <td class="right">{{ number_format( isset($ln['LineTotal']) ? $ln['LineTotal'] : ((($ln['Quantity'] ?? $ln['QuantityOrdered'] ?? 0) * ($ln['Price'] ?? $ln['UnitPrice'] ?? 0))), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px; margin-top:10px;">
            <div style="flex:1">
                <div class="small">Notas</div>
                <div style="margin-top:6px; color:#333">{{ $po['Comments'] ?? '-' }}</div>
            </div>

            <div class="totals">
                <div class="box">
                    <div class="row"><div>Subtotal</div><div class="right">{{ number_format($subtotal ?? 0, 2) }}</div></div>
                    <div class="row"><div>Descuento</div><div class="right">{{ number_format($discount ?? 0, 2) }}</div></div>
                    <div class="row"><div>Impuestos</div><div class="right">{{ number_format($tax ?? 0, 2) }}</div></div>
                    <div class="row total"><div>Total</div><div class="right">{{ number_format($docTotal ?? ($po['DocTotal'] ?? 0), 2) }}</div></div>
                </div>
            </div>
        </div>

        <div class="footer">
            <div>Generado: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} — Folio: {{ $po['DocNum'] ?? '-' }}</div>
            <div style="margin-top:6px">Por favor confirme recepción y condiciones. Si tiene dudas, contacte al departamento de compras.</div>
        </div>
    </div>
</body>
</html>
