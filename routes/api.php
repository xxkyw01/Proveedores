<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Almacen\SapRecepcionController;
use App\Http\Controllers\SapGrpoController;
use App\Http\Controllers\Almacen\GestionSupplierController;

use App\Services\SAPServiceLayer;
use GuzzleHttp\Exception\RequestException;




Route::get('/sap/ping', function (SAPServiceLayer $sl) {
    try {
        $r = $sl->request('GET', 'BusinessPartners?$top=1');
        return response()->json(json_decode($r->getBody()->getContents(), true));
    } catch (RequestException $e) {
        $status = optional($e->getResponse())->getStatusCode() ?? 500;
        $body   = optional($e->getResponse())->getBody()?->getContents();
        Log::error('SL ping error', [
            'status' => $status,
            'msg'    => $e->getMessage(),
            'body'   => $body,
        ]);
        return response()->json([
            'ok'    => false,
            'error' => $e->getMessage(),
            'body'  => json_decode($body, true) ?? $body,
        ], $status);
    } catch (\Throwable $e) {
        Log::error('SL ping fatal', ['msg' => $e->getMessage()]);
        return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
    }
});


Route::get('/sap/ping2', function () {
    $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://192.168.2.214:50000/b1s/v1/',
        'verify'   => false,
        'timeout'  => 30,
        'cookies'  => true,
    ]);
    // login
    $client->post('Login', ['json' => [
        'CompanyDB' => env('SAPB1SL_COMPANYDB'),
        'UserName'  => env('SAPB1SL_USERNAME'),
        'Password'  => env('SAPB1SL_PASSWORD'),
    ]]);
    // ping
    $r = $client->get('BusinessPartners?$top=1', ['headers' => ['Accept' => 'application/json']]);
    return response()->json(json_decode($r->getBody()->getContents(), true));
});

Route::get('/health', fn() => response()->json(['ok' => true]));


Route::get('/sap/po/open', function (Request $r, SAPServiceLayer $sl) {
    $cardCode = $r->query('cardCode');
    $docNum   = $r->query('docNum');

    $filter = "DocumentStatus eq 'bost_Open'";
    if ($cardCode) $filter .= " and CardCode eq '$cardCode'";
    $q = "PurchaseOrders?\$select=DocEntry,DocNum,CardCode,CardName,DocDate"
        . "&\$filter=$filter&\$orderby=DocDate desc&\$top=20";
    $res = $sl->request('GET', $q);
    return response()->json(json_decode($res->getBody(), true));
});

Route::get('/sap/warehouses', function (SAPServiceLayer $sl) {
    $q = "Warehouses?\$select=WarehouseCode,WarehouseName&\$orderby=WarehouseCode";
    $res = $sl->request('GET', $q);
    return response()->json(json_decode($res->getBody(), true));
});

Route::get('/sap/bins', function (Request $r, SAPServiceLayer $sl) {
    $whs = $r->query('whs');
    $q = "BinLocations?\$select=AbsEntry,BinCode,Warehouse&\$filter=Warehouse eq '$whs'&\$top=200";
    $res = $sl->request('GET', $q);
    return response()->json(json_decode($res->getBody(), true));
});


Route::get('/sap/po/{docEntry}/lines', function ($docEntry, SAPServiceLayer $sl) {
    try {
        // Sin $select (algunas versiones no lo soportan en esta navegación)
        $q = "PurchaseOrders($docEntry)/DocumentLines";
        $res  = $sl->request('GET', $q);
        $json = json_decode($res->getBody()->getContents(), true);

        // SL devuelve { value: [...] }. Proyectamos en PHP lo que necesitamos:
        $lines = collect($json['value'] ?? [])
            ->map(fn($l) => [
                'LineNum'         => $l['LineNum']         ?? null,
                'ItemCode'        => $l['ItemCode']        ?? null,
                'ItemDescription' => $l['ItemDescription'] ?? null,
                'Quantity'        => $l['Quantity']        ?? null,
                'OpenQuantity'    => $l['OpenQuantity']    ?? null,
                'WarehouseCode'   => $l['WarehouseCode']   ?? null,
            ])->values();

        return response()->json($lines, 200);
    } catch (RequestException $e) {
        $status = optional($e->getResponse())->getStatusCode() ?? 500;
        $body   = optional($e->getResponse())->getBody()?->getContents();
        return response()->json([
            'ok'    => false,
            'error' => $e->getMessage(),
            'body'  => json_decode($body, true) ?? $body,
        ], $status);
    }
});

Route::get('/sap/po/{docEntry}/lines/raw', function ($docEntry, \App\Services\SAPServiceLayer $sl) {
    $q = "PurchaseOrders($docEntry)/DocumentLines";
    $res  = $sl->request('GET', $q);
    return response()->json(json_decode($res->getBody()->getContents(), true), 200);
});

Route::get('/sap/items/{itemCode}', function ($itemCode, SAPServiceLayer $sl) {
    try {

        $q = "Items?\$select=ItemCode,ItemName,ManageBatchNumbers,ManageSerialNumbers"
            . "&\$filter=ItemCode eq '" . rawurlencode($itemCode) . "'";

        $res = $sl->request('GET', $q);
        $data = json_decode($res->getBody()->getContents(), true);
        if (isset($data['value'])) {
            return response()->json($data['value'][0] ?? null, 200);
        }
        return response()->json($data, 200);
    } catch (RequestException $e) {
        $status = optional($e->getResponse())->getStatusCode() ?? 500;
        $body   = optional($e->getResponse())->getBody()?->getContents();
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage(),
            'body' => json_decode($body, true) ?? $body,
        ], $status);
    }
});

Route::get('/sap/items/by-code/{code}', function ($code, SAPServiceLayer $sl) {
    $q = "Items?\$filter=ItemCode eq '" . rawurlencode($code) . "'";
    $res  = $sl->request('GET', $q);
    $it   = (json_decode($res->getBody()->getContents(), true)['value'][0] ?? null);
    if (!$it) return response()->json(null, 404);

    return response()->json([
        'ItemCode'           => $it['ItemCode'] ?? null,
        'ItemName'           => $it['ItemName'] ?? null,
        'ManageBatchNumbers' => $it['ManageBatchNumbers'] ?? 'tNO',
        'ManageSerialNumbers' => $it['ManageSerialNumbers'] ?? 'tNO',
    ]);
});

Route::get('/sap/po/{docNum}/grpo-candidate', function ($docNum, SAPServiceLayer $sl) {
    try {

        $q1 = "PurchaseOrders?\$select=DocEntry,DocNum,CardCode,CardName"
            . "&\$filter=DocNum eq $docNum and DocumentStatus eq 'bost_Open'&\$top=1";
        $r1  = $sl->request('GET', $q1);
        $poH = json_decode($r1->getBody()->getContents(), true)['value'][0] ?? null;

        if (!$poH) {
            return response()->json(['ok' => false, 'msg' => "OC $docNum no abierta o inexistente"], 404);
        }

        $docEntry = $poH['DocEntry'];
        $q2 = "PurchaseOrders($docEntry)/DocumentLines";
        $r2 = $sl->request('GET', $q2);
        $linesRaw = json_decode($r2->getBody()->getContents(), true)['value'] ?? [];

        $lines = collect($linesRaw)
            ->map(fn($l) => [
                'BaseLine'       => $l['LineNum']       ?? null,
                'ItemCode'       => $l['ItemCode']      ?? null,
                'ItemName'       => $l['ItemDescription'] ?? null,
                'OpenQuantity'   => $l['OpenQuantity']  ?? 0,
                'WarehouseCode'  => $l['WarehouseCode'] ?? null,
            ])
            ->filter(fn($l) => ($l['OpenQuantity'] ?? 0) > 0)
            ->values();

        return response()->json([
            'ok'       => true,
            'po'       => [
                'DocNum'   => $poH['DocNum'],
                'DocEntry' => $docEntry,
                'CardCode' => $poH['CardCode'],
                'CardName' => $poH['CardName'],
            ],

            'lines'    => $lines->map(fn($x) => [
                'BaseLine'      => $x['BaseLine'],
                'Quantity'      => $x['OpenQuantity'],
                'WarehouseCode' => $x['WarehouseCode'],
                'ItemCode'      => $x['ItemCode'],
                'ItemName'      => $x['ItemName'],
            ]),

        ]);
    } catch (RequestException $e) {
        $status = optional($e->getResponse())->getStatusCode() ?? 500;
        $body   = optional($e->getResponse())->getBody()?->getContents();
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage(),
            'body' => json_decode($body, true) ?? $body,
        ], $status);
    }
});

Route::get(
    '/almacen/recepcion/po/{docNum}',
    [GestionSupplierController::class, 'sapGetPO']

    
);


    Route::get('/recepcion/po/{docNum}',[GestionSupplierController::class, 'sapGetPO']);
    Route::post('/recepcion/grpo', [GestionSupplierController::class, 'sapCrearGRPO']);
    Route::post('/almacen/recepcion/grpo/validar', [GestionSupplierController::class, 'sapValidarGRPO'])->name('almacen.grpo.validar');
    Route::post('/almacen/recepcion/grpo', [GestionSupplierController::class, 'crearGRPO'])->name('almacen.grpo.crear');
