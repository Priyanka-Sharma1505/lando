<?php

namespace App\Services;

use App\Enums\RequestType;
use App\Models\Client;
use App\Models\Odoo\Location;
use App\Models\Odoo\Partner;
use App\Models\Odoo\Stock;
use App\Models\Request as VanWijkRequest;

class ClientService {

    public function create(){
        request()->validate([
            'id' => 'required',
            'user_id' => 'required',
        ]);

        $partners = (new Partner())->get();
        $partner = $partners->where('id', request()->input('id'));

        if( $partner->isEmpty() ){
            $name = ' ';
        } else {
            $name = $partner->first()['display_name'];
        }

        $result = Client::create([
            'name' => $name,
            'user_id' => request()->input('user_id'),
            'odoo_id' => request()->input('id'),
        ]);

        return $result;
    }

    public function createRequest(){
        request()->validate([
            'user_id' => 'required',
            'name' => 'required',
            'website' => 'required|url',
            'city' => 'required',
            'zip' => 'required',
        ]);

        $client = Client::create([
            'name' => request()->input('name'),
            'user_id' => request()->input('user_id'),
            'website' => request()->input('website'),
            'city' => request()->input('city'),
            'zip' => request()->input('zip'),
        ]);

        $request = $client->request()->create([
            'status' => RequestType::OPEN,
            'requested_by' => auth('api')->user()->id,
        ]);

        return $request;
    }

    public function delete(){
        request()->validate([
            'odoo_id' => 'required',
            'user_id' => 'required',
        ]);

        if( auth('api')->user()->id == request()->input('user_id') || auth('api')->user()->isAdmin() ){
            return Client::where('user_id', request()->input('user_id'))->where('odoo_id', request()->input('odoo_id'))->delete();
        }
    }

    public function decline(){
        request()->validate([
            'id' => 'required',
        ]);

        if( auth('api')->user()->isAdmin() ){
            $request = VanWijkRequest::findOrFail(request()->input('id'));
            $request->status = RequestType::REFUSED;

            return $request->save();
        }
    }

    public function accept(){
        request()->validate([
            'id' => 'required',
            'odoo_id' => 'required',
        ]);

        if( auth('api')->user()->isAdmin() ){
            $request = VanWijkRequest::findOrFail(request()->input('id'));
            $request->status = RequestType::CLOSED;
            $request->save();

            $request->requestable->odoo_id = request()->input('odoo_id');
            return $request->requestable->save();
        }
    }

    public function requests(){
        return VanWijkRequest::where('requestable_type', 'App\Models\Client')->whereIn('status',[ RequestType::OPEN ])->get();
    }

}
