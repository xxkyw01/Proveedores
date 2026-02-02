<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGrpoRequest;
use App\Services\Sap\GrpoService;

class SapGrpoController extends Controller
{
    public function store(StoreGrpoRequest $req, GrpoService $svc)
    {
        /** @var \Illuminate\Http\Request|\App\Http\Requests\StoreGrpoRequest $req */
        if ($req->boolean('dry_run')) {
            return response()->json([
                'ok'      => true,
                'dry_run' => true,
                'payload' => $svc->makePayload($req->validated())
            ], 200);
        }


        $result = $svc->createFromPo($req->validated());
        return response()->json($result, $result['ok'] ? 201 : 400);
    }
}
