<?php

namespace App\Services\Sap;

use App\Services\SAPServiceLayer;
use GuzzleHttp\Exception\RequestException;

class GrpoService
{
    public function makePayload(array $input): array
    {
        // Solo construimos. El createFromPo hace el bucle por cada doc.
        $payloads = [];

        foreach ($input['docs'] as $doc) {
            $po = (int) $doc['po_docentry'];
            $header = $doc['header'] ?? [];

            $slDoc = [
                // Cabecera GRPO (ajusta campos que uses)
                'DocDate'   => now()->format('Y-m-d'),
                'Comments'  => $header['Comments']   ?? null,
                'U_RefExt'  => $header['U_RefExt']   ?? null,

                // Líneas
                'DocumentLines' => [],
            ];

            foreach ($doc['lines'] as $l) {
                $line = [
                    'BaseEntry'  => $po,                       
                    'BaseLine'   => (int) $l['BaseLine'],     
                    'Quantity'   => (float) $l['Quantity'],
                    'WarehouseCode' => $l['WarehouseCode'],
                ];

                // Lotes
                if (!empty($l['Batches'])) {
                    $line['BatchNumbers'] = array_map(function($b){
                        return [
                            'BatchNumber' => $b['BatchNumber'],
                            'Quantity'    => (float) $b['Quantity'],
                        ];
                    }, $l['Batches']);
                }

                // Series
                if (!empty($l['Serials'])) {
                    $line['SerialNumbers'] = array_map(function($s){
                        return [
                            'InternalSerialNumber' => $s['SerialNumber'],
                        ];
                    }, $l['Serials']);
                }

                // Bins
                if (!empty($l['Bins'])) {
                    $line['DocumentLinesBinAllocations'] = array_map(function($b){
                        return [
                            'BinAbsEntry' => (int) $b['BinAbsEntry'],
                            'Quantity'    => (float) $b['Quantity'],
                        ];
                    }, $l['Bins']);
                }

                $slDoc['DocumentLines'][] = $line;
            }

            $payloads[] = $slDoc;
        }

        return $payloads;
    }

    public function createFromPo(array $input): array
    {
        $out = ['ok'=>true,'results'=>[]];

        $payloads = $this->makePayload($input);

        foreach ($payloads as $idx => $pdn) {
            try {
                $res = $this->sl->request('POST', 'PurchaseDeliveryNotes', ['json' => $pdn]);
                $json = json_decode($res->getBody()->getContents(), true);

                $out['results'][] = [
                    'index'    => $idx,
                    'status'   => 'created',
                    'DocEntry' => $json['DocEntry'] ?? null,
                    'DocNum'   => $json['DocNum']   ?? null,
                    'raw'      => $json,
                ];
            } catch (RequestException $e) {
                $body = optional($e->getResponse())->getBody()?->getContents();
                $out['ok'] = false;
                $out['results'][] = [
                    'index'  => $idx,
                    'status' => 'error',
                    'error'  => $e->getMessage(),
                    'body'   => json_decode($body, true) ?? $body,
                    'payload' => $pdn, // útil para depurar
                ];
            }
        }

        return $out;
    }
}
