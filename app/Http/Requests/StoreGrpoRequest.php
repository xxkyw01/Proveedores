<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Services\SAPServiceLayer;

class StoreGrpoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'docs'                           => ['required','array','min:1'],
            'docs.*.po_docentry'             => ['required','integer'],
            'docs.*.header'                  => ['sometimes','array'],
            'docs.*.header.U_RefExt'         => ['sometimes','string','max:50'],
            'docs.*.header.Comments'         => ['sometimes','string','max:254'],

            'docs.*.lines'                   => ['required','array','min:1'],
            'docs.*.lines.*.BaseLine'        => ['required','integer','min:0'],
            'docs.*.lines.*.Quantity'        => ['required','numeric','min:0.0001'],
            'docs.*.lines.*.WarehouseCode'   => ['required','string','max:8'],

            // Lotes
            'docs.*.lines.*.Batches'                         => ['sometimes','array','min:1'],
            'docs.*.lines.*.Batches.*.BatchNumber'           => ['required_with:docs.*.lines.*.Batches','string','max:100'],
            'docs.*.lines.*.Batches.*.Quantity'              => ['required_with:docs.*.lines.*.Batches','numeric','min:0.0001'],

            // Series
            'docs.*.lines.*.Serials'                          => ['sometimes','array','min:1'],
            'docs.*.lines.*.Serials.*.SerialNumber'           => ['required_with:docs.*.lines.*.Serials','string','max:100'],

            // Bins
            'docs.*.lines.*.Bins'                             => ['sometimes','array','min:1'],
            'docs.*.lines.*.Bins.*.BinAbsEntry'               => ['required_with:docs.*.lines.*.Bins','integer','min:1'],
            'docs.*.lines.*.Bins.*.Quantity'                  => ['required_with:docs.*.lines.*.Bins','numeric','min:0.0001'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            /** @var SAPServiceLayer $sl */
            $sl = app(SAPServiceLayer::class);

            $docs = $this->input('docs', []);
            $seen = []; 

            foreach ($docs as $di => $doc) {
                $po = (int)($doc['po_docentry'] ?? 0);
                if (!$po) { $v->errors()->add("docs.$di.po_docentry", "DocEntry inválido."); continue; }


                try {
                    $hdr = json_decode($sl->request('GET', "PurchaseOrders($po)?\$select=DocEntry,DocumentStatus")->getBody(), true);
                    if (($hdr['DocumentStatus'] ?? '') !== 'bost_Open') {
                        $v->errors()->add("docs.$di.po_docentry", "La OC $po no está abierta.");
                        continue;
                    }
                    $linesRaw = json_decode($sl->request('GET', "PurchaseOrders($po)/DocumentLines")->getBody(), true)['value'] ?? [];
                    $byLine = collect($linesRaw)->keyBy('LineNum');

                } catch (\Throwable $e) {
                    $v->errors()->add("docs.$di.po_docentry", "No se pudo consultar la OC $po en SAP.");
                    continue;
                }
 
                foreach (($doc['lines'] ?? []) as $li => $line) {
                    $bl  = (int)($line['BaseLine'] ?? -1);
                    $qty = (float)($line['Quantity'] ?? 0);
                    $whs = (string)($line['WarehouseCode'] ?? '');

                    $key = "$po|$bl";
                    if (isset($seen[$key])) {
                        $v->errors()->add("docs.$di.lines.$li.BaseLine", "La línea $bl ya está incluida en el payload.");
                    } else {
                        $seen[$key] = true;
                    }

                    if (!$byLine->has($bl)) {
                        $v->errors()->add("docs.$di.lines.$li.BaseLine", "La línea $bl no existe en la OC $po.");
                        continue;
                    }

                    $slLine = $byLine->get($bl);
                    $openQ  = (float)($slLine['OpenQuantity'] ?? 0);
                    $item   = (string)($slLine['ItemCode'] ?? '');


                    if ($qty > $openQ + 1e-6) {
                        $v->errors()->add("docs.$di.lines.$li.Quantity", "Cantidad $qty excede el pendiente $openQ en línea $bl.");
                    }

                    $it = json_decode($sl->request('GET', "Items?\$filter=ItemCode eq '".rawurlencode($item)."'")->getBody(), true)['value'][0] ?? null;
                    $isBatch = (($it['ManageBatchNumbers'] ?? 'tNO') === 'tYES');
                    $isSerial= (($it['ManageSerialNumbers'] ?? 'tNO') === 'tYES');

                    if ($isBatch) {
                        $batches = collect($line['Batches'] ?? []);
                        if ($batches->isEmpty()) {
                            $v->errors()->add("docs.$di.lines.$li.Batches", "Item $item requiere lotes.");
                        }
                        $sumB = $batches->sum(fn($b)=> (float)($b['Quantity'] ?? 0));
                        if (abs($sumB - $qty) > 1e-6) {
                            $v->errors()->add("docs.$di.lines.$li.Batches", "Suma de lotes ($sumB) debe igualar Quantity ($qty).");
                        }
                    }

                    if ($isSerial) {
                        $serials = collect($line['Serials'] ?? []);
                        if ($serials->count() !== (int)round($qty)) {
                            $v->errors()->add("docs.$di.lines.$li.Serials", "Se requieren ".(int)$qty." series para Quantity=$qty.");
                        }
                    }

                    // Bins (si vienen)
                    if (!empty($line['Bins'])) {
                        $bins = collect($line['Bins']);
                        $sumBins = $bins->sum(fn($b)=> (float)($b['Quantity'] ?? 0));
                        if (abs($sumBins - $qty) > 1e-6) {
                            $v->errors()->add("docs.$di.lines.$li.Bins", "Suma de bins ($sumBins) debe igualar Quantity ($qty).");
                        }
                        // (opcional) validar que cada BinAbsEntry existe
                        // $sl->request('GET', "BinLocations($absEntry)")
                    }

                    // (opcional) validar WarehouseCode coincide con el de la OC
                    if (!empty($slLine['WarehouseCode']) && $whs !== $slLine['WarehouseCode']) {
                        // Si tu negocio no permite cambiar almacén:
                        // $v->errors()->add("docs.$di.lines.$li.WarehouseCode", "Debe ser ".$slLine['WarehouseCode']);
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'docs.required' => 'Debes enviar al menos un documento.',
            'docs.*.lines.required' => 'Cada documento requiere líneas.',
        ];
    }
}
