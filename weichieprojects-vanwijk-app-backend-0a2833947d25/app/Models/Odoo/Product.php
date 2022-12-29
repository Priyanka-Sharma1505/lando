<?php

namespace App\Models\Odoo;

class Product extends Base
{
    public $resource = 'product.template';
    public $fields = [
        'id',
        'name',
        'type',
        'categ_id',
        'is_product_variant',
        'sale_ok',
        'product_variant_ids',
        'qty_available',
        'taxes_id',
        'price',
        'x_studio_article_name_spreadsheets',
        'x_studio_kist_lijn',
        'x_studio_verkocht_in_portaal',
        /*'product_template_attribute_value_ids',*/
        /*'combination_indices',*/
        /*'image_256',*/
    ];

    public function __construct($data=null)
    {
        $this->data = $data;
    }

    /** @TODO: Isn't cached yet */
    public function all(){
        return $this->connect()->where('type', '=', 'product')->fields($this->fields)->get($this->resource);
    }

    public function delivery(){
        return $this->connect()->where('type', '=', 'service')->where('categ_id', 103)->fields($this->fields)->get($this->resource);
    }

    public function pickup(){
        return $this->connect()->where('type', '=', 'service')->fields($this->fields)->get($this->resource);
    }

    public function collect(){
        $data = collect( $this->data );
        $data = $data->map(function ($item){
            $item['street_number'] = ($item['street_number']==false) ? '' : $item['street_number'];
            return $item;
        });
        return $data;
    }
}
