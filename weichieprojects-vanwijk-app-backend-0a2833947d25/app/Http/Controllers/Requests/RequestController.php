<?php

namespace App\Http\Controllers\Requests;

use App\Enums\RequestType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as VanWijkRequest;

class RequestController extends Controller
{
    /*protected $middleware = [
        ['permission:view requests']
    ];*/

    public function __construct(){
        $this->middleware(['can:view requests']);
    }

    public function all(){
        $requests = VanWijkRequest::where('status', RequestType::OPEN)->get();

        return $requests->map(function($request){
            return [
                'type' => str_replace('App\\Models\\', '', $request['requestable_type']),
            ];
        })->groupBy('type')->map->count();
    }
}
