<?php

namespace App\Models\Odoo;

use App\Models\Odoo\Attributes\Length;
use App\Models\Odoo\Attributes\Width;

class PricelistItem extends Base
{
    public $resource = 'product.pricelist.item';
    public $fields = [
        'id',
        'name',
        'product_id',
        'product_tmpl_id',
        'min_quantity',
        'currency_id',
        'price_surcharge',
        'price_discount',
        'price',
        'fixed_price',
        'company_id',
        'active',
        'applied_on',
    ];

    public function all(){
        return $this->cache('pricelist-items', function() {
            return $this->connect()->where('active', true)->fields($this->fields)->get($this->resource);
        }); // , true
    }

    public function byProductId($product_id){
        return $this->cache('pricelist-items-'.$product_id, function() use($product_id) {
            return $this->connect()->where('product_id', $product_id)->where('active', true)->fields($this->fields)->get($this->resource);
        }); //, true
    }

    public function fromPricelistId($id){
        return $this->cache('pricelist-items-' . $id, function() use($id) {

            //->where('fixed_price', '>', 1)
            // Fixed price will be zero for some pricelist items
            return $this->connect()->where('pricelist_id', $id)->where('active', true)->fields($this->fields)->get($this->resource);
        }, true); //, true
    }

    public function __construct($data=null)
    {
        $this->data = $data;
    }
}
