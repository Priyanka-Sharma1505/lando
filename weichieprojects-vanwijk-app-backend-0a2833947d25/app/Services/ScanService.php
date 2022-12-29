<?php

namespace App\Services;

use App\Models\Odoo\Location;
use App\Models\Odoo\Stock;
use http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ScanService {

    public $trusted_codes = [
        '20221090915654',
        '20221090915650',
        '20211090105654',
        '20211080105654',
    ];

    public $deregistered_codes = [
        '20221090915620',
        '20221090915621',
        '20211090105622',
        '20211080105623',
    ];

    public function __construct()
    {
        Cache::remember('codes_used', 60*15, function(){
           return collect([]);
        });
    }

    public function deregister(){

        request()->validate([
           'code' => 'required'
        ]);

        if( in_array( request()->input('code'), $this->trusted_codes ) ) {

            $data = [
                'lot_id' => request()->input('code'),
                'name' => 'Eminent Klassiek Eiken verhoogd - 356',
                'description' => 'Simplessa - 31 Ongebleekt katoen zonder koord',
                'length' => '210 cm (1x verlengd)',
                'width' => '63 cm (1x verbreed)',
            ];

            return response()->json($data);
        } else {
            return response()->json(['error' => 'Couldn\'t find this id'], 404);
        }

    }

    public function deregisterComplete(){

        request()->validate([
            'code' => 'required',
            'undertaker' => 'required',
            'deceased' => 'required',
            'file_id' => 'required',
        ]);

        if( in_array( request()->input('code'), Cache::get('codes_used')->toArray() ) ){
            $data = ['error' => 'This id has been deregistered already'];
            return response()->json($data, 405);
        }

        if( in_array( request()->input('code'), $this->trusted_codes ) ) {

            /* We'll trigger deregister service here */

            $data = ['success' => request()->input('code') . ' has been deregistered successfully'];

            Cache::put('codes_used', Cache::get('codes_used')->push(request()->input('code')));

            return response()->json($data);
        } else {
            return response()->json(['error' => 'Couldn\'t find this id'], 404);
        }
    }

    public function deregisterCancel(){
        request()->validate([
            'code' => 'required'
        ]);

        if( in_array( request()->input('code'), $this->deregistered_codes ) ) {

            $data = [
                'lot_id' => request()->input('code'),
                'name' => 'Eminent Klassiek Eiken verhoogd - 356',
                'description' => 'Simplessa - 31 Ongebleekt katoen zonder koord',
                'length' => '210 cm (1x verlengd)',
                'width' => '63 cm (1x verbreed)',
            ];

            return response()->json($data);
        } else {
            return response()->json(['error' => 'Couldn\'t find this id'], 404);
        }
    }

    public function deregisterCancelComplete(){

        $validator = Validator::make(request()->all(), [
            'code' => 'required',
            'reason' => 'required',
        ]);

        if( $validator->fails() ){
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 400);
        }

        if( in_array( request()->input('code'), Cache::get('codes_used')->toArray() ) ){
            $data = ['error' => 'This id has ben cancelled already'];
            return response()->json($data, 405);
        }

        if( in_array( request()->input('code'), $this->deregistered_codes ) ) {

            /* We'll trigger deregister service here */

            $data = ['success' => request()->input('code') . ' has ben cancelled successfully.'];

            Cache::put('codes_used', Cache::get('codes_used')->push(request()->input('code')));

            return response()->json($data);
        } else {
            return response()->json(['error' => 'Couldn\'t find this id'], 404);
        }
    }

    public function return(){

        request()->validate([
            'code' => 'required'
        ]);

        if( in_array( request()->input('code'), $this->trusted_codes ) ) {

            $data = [
                'lot_id' => request()->input('code'),
                'name' => 'Eminent Klassiek Eiken verhoogd - 356',
                'description' => 'Simplessa - 31 Ongebleekt katoen zonder koord',
                'length' => '210 cm (1x verlengd)',
                'width' => '63 cm (1x verbreed)',
            ];

            return response()->json($data);
        } else {
            return response()->json(['error' => 'Couldn\'t find this id'], 404);
        }
    }

    public function returnComplete(){

        $validator = Validator::make(request()->all(), [
            'code' => 'required',
            'reason' => 'required',
            'files' => 'mimes:jpg,jpeg,png,pdf|max:20000',
        ]);

        if( $validator->fails() ){
            return response()->json([
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray(),
            ], 400);
        }

        if( in_array( request()->input('code'), Cache::get('codes_used')->toArray() ) ){
            $data = ['error' => 'This id has ben returned already'];
            return response()->json($data, 405);
        }

        if( in_array( request()->input('code'), $this->trusted_codes ) ) {

            /* We'll trigger deregister service here */

            $data = ['success' => request()->input('code') . ' has been returned successfully'];

            Cache::put('codes_used', Cache::get('codes_used')->push(request()->input('code')));

            return response()->json($data);
        } else {
            return response()->json(['error' => 'Couldn\'t find this id'], 404);
        }

    }



}
