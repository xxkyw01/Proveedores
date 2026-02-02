<?php

namespace App\Http\Controllers\SAP;

use App\Http\Controllers\Controller;
use DateTimeZone;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use App\Services\SapSessionService;


class SAPServiceLayerController extends Controller
{
    public function getClients()
    {
        $client = new Client(['verify' => false, 'base_uri' => 'https://192.168.2.214:50000/b1s/v1']);

        $data = array(
            'CompanyDB' => 'BDMPC', 
            'UserName' => 'manager', 
            'Password' => '1A2b2021' 
        );

        try {
            $response = $client->request('POST', 'Login', [
                'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'body'    => json_encode($data)
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $responseBodyAsString = $response->getBody()->getContents();
            echo $response->getStatusCode() . PHP_EOL;
            echo $responseBodyAsString;
        }

        if ($response->getStatusCode() === 200) {

            $SetCookie = json_encode($response->getHeader('Set-Cookie'));
            $remplace = array('[', '"', ']');
            $NewCookies = str_replace($remplace, '', $SetCookie);
            $Cookie = explode(",", $NewCookies);

            $quotationData = array(
                'CardCode' => 'CJ00195',
                "Comments" => "Intranet Test Orders by Service Layer",
                //"DocDueDate" => "20240820",
                'DocumentLines' => array([
                    "ItemCode" => "00013",
                    "Quantity" => 1,
                    "WarehouseCode" => "INV BOSQ",
                    "UoMEntry" => 1
                ])
            );

            $orderData = array(
                'CardCode' => 'CJ00195',
                "Comments" => "Intranet Test Orders by Service Layer Basado en Ofertas de ventas 138548.",
                "DocDueDate" => "20240820",
                "Series" => 9,
                "NumAtCard" => 'OC-4300148775',
                "PeyMethod" => '99',
                "ShipToCode" => 'ENTREGA DOLCE NATURA',
                'DocumentLines' => array([
                    "ItemCode" => "00013",
                    "Quantity" => 1,
                    "WarehouseCode" => "INV BOSQ",
                    "UoMEntry" => 1,
                    "BaseType" => 23,
                    "BaseLine" => 0,
                    "BaseRef" => 138548,
                    "BaseEntry" => 138548
                ])
            );

            $DeliveryNotes = array(
                'CardCode' => 'CJ00195',
                "Comments" => "Intranet Test Orders by Service Layer Basado en Ofertas de ventas 138549. Basado en Pedidos de cliente 61235.",
                "DocDueDate" => "20240310",
                "Series" => 7,
                'DocumentLines' => array([
                    "ItemCode" => "00013",
                    "Quantity" => 1,
                    "WarehouseCode" => "INV BOSQ",
                    "UoMEntry" => 1,
                    "BaseType" => 17,
                    "BaseLine" => 0,
                    "BaseRef" => 154383,
                    "BaseEntry" => 154383
                ])
            );

            $InventoryTransferRequests = array(
                "Series" => 173,
                "DocDate" => "20250310",
                "DueDate" =>  "20250317",
                "SalesPersonCode" => -1,
                "Reference2" => "Intranet",
                "Comments" => "Prueba Tranferencias INV BOSQ - QLeon",
                "JournalMemo" => "Solicitud de traslado - ",
                "PriceList" => -2,
                "FromWarehouse" => "INV BOSQ",
                "ToWarehouse" => "QLeon",

                'StockTransferLines' => array(
                    [
                        "ItemCode" => "02302",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1
                    ],
                    [
                        "ItemCode" => "01672",
                        "Quantity" => 72,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1
                    ],
                    [
                        "ItemCode" => "04156",
                        "Quantity" => 200,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1
                    ],
                    [
                        "ItemCode" => "02568",
                        "Quantity" => 24,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1
                    ],
                    [
                        "ItemCode" => "05583",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1
                    ],
                    [
                        "ItemCode" => "01503",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1
                    ],
                    [
                        "ItemCode" => "01511",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1
                    ]
                )
            );

            /*
            $StockTransfers = array(
                "Series" => 176,
                "DocDate" => "20250310",
                "Reference2" => "Intranet",
                "Comments" => "Prueba Tranferencias INV BOSQ - QLeon Basado en Solicitud de traslado 2001368.",
                "JournalMemo" => "Inventory Transfers -",
                "PriceList" => -2,
                "FromWarehouse" => "INV BOSQ",
                "ToWarehouse" => "QLeon",

                'StockTransferLines' => array(
                    [
                        "ItemCode" => "02302",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1,
                        "BaseType" => 1250000001,
                        "BaseLine" => 0,
                        "BaseEntry" => 47736
                    ],
                    [
                        "ItemCode" => "01672",
                        "Quantity" => 72,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1,
                        "BaseType" => 1250000001,
                        "BaseLine" => 1,
                        "BaseEntry" => 47736
                    ],
                    [
                        "ItemCode" => "04156",
                        "Quantity" => 200,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1,
                        "BaseType" => 1250000001,
                        "BaseLine" => 2,
                        "BaseEntry" => 47736
                    ],
                    [
                        "ItemCode" => "02568",
                        "Quantity" => 24,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1,
                        "BaseType" => 1250000001,
                        "BaseLine" => 3,
                        "BaseEntry" => 47736
                    ],
                    [
                        "ItemCode" => "05583",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1,
                        "BaseType" => 1250000001,
                        "BaseLine" => 4,
                        "BaseEntry" => 47736
                    ],
                    [
                        "ItemCode" => "01503",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1,
                        "BaseType" => 1250000001,
                        "BaseLine" => 5,
                        "BaseEntry" => 47736
                    ],
                    [
                        "ItemCode" => "01511",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "INV BOSQ",
                        "WarehouseCode" => "QLeon",
                        "UoMEntry" => 1,
                        "BaseType" => 1250000001,
                        "BaseLine" => 6,
                        "BaseEntry" => 47736
                    ]
                )
            );
*/

            $StockTransfers = array(
                "Series" => 177,
                "DocDate" => "20250310",
                "Reference2" => "Intranet",
                "Comments" => "Prueba Tranferencias QLeon - INV2MLEO Basado en Transferencia de Stock 2042932.",
                "JournalMemo" => "Inventory Transfers -",
                "PriceList" => -2,
                "FromWarehouse" => "QLeon",
                "ToWarehouse" => "INV2MLEO",

                'StockTransferLines' => array(
                    [
                        "ItemCode" => "02302",
                        "Quantity" => 10,
                        "FromWarehouseCode" => "QLeon",
                        "WarehouseCode" => "INV2MLEO",
                        "UoMEntry" => 1,
                        "BaseType" => 67,
                        "BaseLine" => 0,
                        "BaseEntry" => 102642
                    ],
                    [
                        "ItemCode" => "01672",
                        "Quantity" => 70,
                        "FromWarehouseCode" => "QLeon",
                        "WarehouseCode" => "INV2MLEO",
                        "UoMEntry" => 1,
                        "BaseType" => 67,
                        "BaseLine" => 1,
                        "BaseEntry" => 102642
                    ],
                    [
                        "ItemCode" => "04156",
                        "Quantity" => 150,
                        "FromWarehouseCode" => "QLeon",
                        "WarehouseCode" => "INV2MLEO",
                        "UoMEntry" => 1,
                        "BaseType" => 67,
                        "BaseLine" => 2,
                        "BaseEntry" => 102642
                    ],
                    [
                        "ItemCode" => "02568",
                        "Quantity" => 24,
                        "FromWarehouseCode" => "QLeon",
                        "WarehouseCode" => "INV2MLEO",
                        "UoMEntry" => 1,
                        "BaseType" => 67,
                        "BaseLine" => 3,
                        "BaseEntry" => 102642
                    ],
                    [
                        "ItemCode" => "05583",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "QLeon",
                        "WarehouseCode" => "INV2MLEO",
                        "UoMEntry" => 1,
                        "BaseType" => 67,
                        "BaseLine" => 4,
                        "BaseEntry" => 102642
                    ],
                    [
                        "ItemCode" => "01503",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "QLeon",
                        "WarehouseCode" => "INV2MLEO",
                        "UoMEntry" => 1,
                        "BaseType" => 67,
                        "BaseLine" => 5,
                        "BaseEntry" => 102642
                    ],
                    [
                        "ItemCode" => "01511",
                        "Quantity" => 12,
                        "FromWarehouseCode" => "QLeon",
                        "WarehouseCode" => "INV2MLEO",
                        "UoMEntry" => 1,
                        "BaseType" => 67,
                        "BaseLine" => 6,
                        "BaseEntry" => 102642
                    ]
                )
            );

            try {
                $response =   $client->request('POST', 'StockTransfers', [
                    'headers' => ['Cookie' => $Cookie, 'Content-Type' => 'application/json', 'charset' => 'utf-8', 'Accept' => 'application/json'],
                    'body'    => json_encode($StockTransfers)
                ]);

                $data = json_decode($response->getBody(), true);
                dd($data);
            } catch (ClientException $e) {
                $response = $e->getResponse();
                $responseError = json_decode($response->getBody(), true);

                $arr_json = array(
                    'statusCode' => $response->getStatusCode(),
                    'reasonPhrase' => $response->getReasonPhrase(),
                    'data' => json_decode($response->getBody(), true),
                    'msg' => $responseError["error"]["code"] . ' | ' . $responseError["error"]["message"]["value"]
                );
                dd($arr_json);
            }
        } else {
            // Hubo un error en la solicitud
            echo 'Error: ' . $response->getStatusCode();
        }
    }
}
