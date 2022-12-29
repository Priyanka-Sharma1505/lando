<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\StockService;
use Illuminate\Http\Request;

class getStockByLocationIdController extends Controller
{
    public function index($id){
        $stock_items = (new StockService())->fromLocation($id);
        return [
            'stock' => $stock_items,
            'stock_list' => $stock_items->groupBy('product_id.0')->transform(function($data, $key){
               return [
                   'stock' => $data,
                   'quantity' => $data->sum('quantity'),
                   'reserved_quantity' => $data->sum('reserved_quantity'),
               ];
            }),
            'attributes' => (new ProductService())->attributePerVariant($stock_items->pluck('product_id.0')),
        ];
    }
}
