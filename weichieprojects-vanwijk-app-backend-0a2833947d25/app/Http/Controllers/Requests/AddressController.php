<?php

namespace App\Http\Controllers\Requests;

use App\Enums\RequestType;
use App\Http\Controllers\Controller;
use App\Models\Request as VanWijkRequest;
use App\Models\UserDeliveryAddress;
use App\Models\UserLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function all(){
        return VanWijkRequest::where('requestable_type', 'App\Models\UserDeliveryAddress')
            ->where('status', RequestType::OPEN)
            ->orWhere('status', RequestType::ON_HOLD)->get();
    }

    public function show($id){
        $signup = VanWijkRequest::findOrFail($id);

        return $signup;
    }

    public function refuse(){

        $this->validate(request(),[
            'id' => 'required'
        ]);

        $request = \App\Models\Request::findOrFail( request()->input('id') );
        $request->refuse();
    }

    public function accept(){
        $this->validate(request(), [
            'data' => 'required',
            'address_id' => 'required',
        ]);

        $request = \App\Models\Request::findOrFail( request()->input('data.id') );
        $request->accept(request()->input('address_id'));
    }

    public function create(){

        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'address_line' => 'required',
            'number' => 'required',
            'city' => 'required',
            'postcode' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // @TODO: Move most to UserLocation model (fat model, skinny contr)

        // Create request for additional address
        $user_address = UserDeliveryAddress::create([
            'user_id' => request()->input('user_id'),
            'name' => request()->input('name'),
            'address_line' => request()->input('address_line'),
            'number' => request()->input('number'),
            'city' => request()->input('city'),
            'postcode' => request()->input('postcode'),
        ]);

        $request = $user_address->request()->create([
            'status' => RequestType::OPEN,
            'requested_by' => auth('api')->user()->id,
        ]);

        return $request;
    }


}
