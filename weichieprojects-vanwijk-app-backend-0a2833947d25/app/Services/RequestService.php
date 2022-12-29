<?php

namespace App\Services;

use App\Models\DepotReturn;
use App\Models\Deregister;
use App\Models\Extension;
use App\Enums\RequestType;
use App\Models\Request as VanWijkRequest;
use App\Services\ProductService;
use Illuminate\Http\Client\Request;
use  App\Models\Odoo\Attributes\Interior;

class RequestService {

    public function deregister(){
        request()->validate([
            'deceased' => 'required',
            'product' => 'required',
            'quantity' => 'required',
            'reason' => 'required',
        ]);

        $product = request()->input('product');

        $deregister = Deregister::create([
            'user_id' => auth()->user()->id,
            'undertaker' => request()->input('undertaker'),
            //'undertaker_id' => '',
            'quantity' => 1,
            'product_id' => $product['product_id'][0],
            'product_data' => json_encode($product),
            'deceased' => request()->input('deceased'),
            'reason' => request()->input('reason'),
            'lot_id' => $product['lot_id'][1],
            'file_number' => request()->input('file_number'),
        ]);

        $request = $deregister->request()->create([
            'status' => RequestType::OPEN,
            'requested_by' => auth('api')->user()->id,
        ]);

        return $request;
    }

    public function deregisterDecline(){
        $request = VanWijkRequest::findOrFail(request()->input('id'));
        $request->status = RequestType::REFUSED;
        return $request->save();
    }

    public function deregisterAccept(){
        $request = VanWijkRequest::findOrFail(request()->input('id'));
        $request->status = RequestType::CLOSED;
        return $request->save();
    }

    public function deregisterRequests()
    {
        return VanWijkRequest::where('requestable_type', 'App\Models\Deregister')->whereIn('status', [RequestType::OPEN])->orderBy('created_at', 'DESC')->get();
    }

    public function return(){
        request()->validate([
            'product_id' => 'required',
            'reason' => 'required',
            'file.*' => 'mimes:jpg,jpeg,png|max:1000000',
        ]);

        $product_data = '';
        $lot_id = '';
        if( request()->input('product_data') ){
            $product_data = request()->input('product_data');
            $lot_id = json_decode($product_data, true)['lot_id'][1];
        }

        $return = DepotReturn::create([
            "product_id" => request()->input('product_id'),
            "product_data" => $product_data,
            "reason" => request()->input('reason'),
            "lot_id" => $lot_id,
        ]);

        if(request()->hasfile('file'))
        {
            foreach(request()->file('file') as $file)
            {
                $return
                    ->addMedia($file)
                    ->toMediaCollection();
            }
        }

        if($return){
            $reqReturn = $return->request()->create([
                'status' => RequestType::OPEN,
                "requested_by" => auth('api')->user()->id,
            ]);

            if($reqReturn){
                $data = ['success' =>  'Return request has been created successfully'];
                return response()->json($data);
            }
        }
        $error = ['error' =>  'Return request failed'];
        return response()->json($error);
    }

    public function returnRequests(){
        return VanWijkRequest::where('requestable_type', 'App\Models\DepotReturn')->whereIn('status',[ RequestType::OPEN ])->orderBy('created_at', 'DESC')->get();
    }

    public function DepotExtension(){
        request()->validate([
            'product_id' => 'required',
            'reason' => 'required',
            'quantity' => 'required',
        ]);

        $extension = Extension::create([
            "product_id" => request()->input('product_id'),
            "reason" => request()->input('reason'),
            "quantity" => request()->input('quantity')
        ]);

        if($extension){
           $reqExtension = $extension->request()->create([
                'status' => RequestType::OPEN,
                "requested_by" => auth('api')->user()->id,
                "requestable_id" => $extension->id,
                "requestable_type" => 'depot_exention'
            ]);

            if($reqExtension){
                $data = ['success' =>  'Depot Extension Request has been created successfully'];
                return response()->json($data);
            }
        }
        $error = ['error' =>  'Depot Extension Request failed'];
        return response()->json($error);
    }

    public function getDepotExtension(){
        $extension = VanWijkRequest::where('requestable_type', 'App\Models\Extension')->where('status',RequestType::OPEN)->orderBy('created_at', 'DESC')->get();
        $data = ['success' =>  'Depost Extension Request has been fetch successfully','data'=>$extension];
        return response()->json($data);
    }

    public function confirmOrReject(){
        request()->validate([
            'id' => 'required',
            'accepted'=> 'required',
        ]);
        if(request()->input('accepted')){
            VanWijkRequest::where('id',request()->input('id'))->update(['status' => RequestType::CLOSED]);
        }else{
            VanWijkRequest::where('id',request()->input('id'))->update(['status' => RequestType::REFUSED]);
        }
        $data = ['success' =>  'Request has been process successfully'];
        return response()->json($data);
    }

    /**
     * Deprecated
     */
    public function getHistory(){
        return $this->deregisterHistory();

        $extension = DepotRequest::get();
        foreach($extension as $req){
            $req->requestable->product=(new ProductService())->getProductVariant([$req->requestable->product_id]) ;
            $product_maat = (new ProductService())->attributePerVariant([$req->requestable->product_id]);
            if(count($product_maat)>0)
            {
                $req->requestable->product_maat = $product_maat[$req->requestable->product_id]['size'];
            }else{
                $req->requestable->product_maat = 'N/A';
            }
        }
        $data = ['success' =>  'Depost Extension Request history has been fetch successfully','data'=>$extension];
        return response()->json($data);
    }

    public function deregisterHistory(){
        if( auth('api')->user()->isAdmin() ){
            return VanWijkRequest::where('requestable_type', 'App\Models\Deregister')->orderBy('created_at', 'DESC')->get();
        }

        return VanWijkRequest::where('requestable_type', 'App\Models\Deregister')->where('requested_by', auth('api')->user()->id )->whereIn('status',[ RequestType::OPEN, RequestType::CLOSED ])->orderBy('created_at', 'DESC')->get();
    }

}
