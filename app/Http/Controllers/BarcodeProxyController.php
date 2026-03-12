<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BarcodeProxyController extends Controller
{
    public function lookup(Request $request)
    {
        $barcode = trim($request->query('barcode',''));
        if(!$barcode){
            return response()->json(['error' => 'barcode required'], 400);
        }

        $cacheKey = 'barcode:lookup:'.md5($barcode);
        $data = Cache::remember($cacheKey, 60 * 24, function() use ($barcode) {
            $apiKey = env('BARCODE_API_KEY');
            if($apiKey){
                $url = "https://api.barcodelookup.com/v3/products?barcode=".urlencode($barcode)."&key=".$apiKey;
                $resp = Http::timeout(8)->get($url);
                if($resp->ok()){
                    $j = $resp->json();
                    return ['source' => 'barcodelookup', 'raw' => $j];
                }
            }

            $offUrl = "https://world.openfoodfacts.org/api/v0/product/".urlencode($barcode).".json";
            $resp = Http::timeout(8)->get($offUrl);
            if($resp->ok()){
                $j = $resp->json();
                if(isset($j['status']) && $j['status'] == 1 && isset($j['product'])){
                    $p = $j['product'];
                    $info = [
                        'source' => 'openfoodfacts',
                        'name' => $p['product_name'] ?? ($p['product_name_en'] ?? ''),
                        'description' => $p['generic_name'] ?? ($p['categories'] ?? ''),
                        'image' => $p['image_small_url'] ?? ($p['image_url'] ?? ''),
                        'raw' => $p,
                    ];
                    return $info;
                }
                return ['source' => 'openfoodfacts', 'found' => false, 'raw' => $j];
            }

            return ['source' => 'none', 'found' => false];
        });

        return response()->json($data);
    }
}
