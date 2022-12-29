<?php

namespace App\Models\Odoo;

class Location extends Base
{
    public $resource = 'stock.location';
    public $fields = [
        'id',
        'name',
        'complete_name',
        'display_name',
        'active',
        'usage',
        'child_ids',
        'warehouse_id',
        'company_id',
        'x_studio_assortiment'
    ];

    public function __construct($data=null)
    {
        $this->data = $data;
    }

    public function all(){
        return $this->cache('locations', function(){
            return $this->connect()->where('usage', 'internal')->fields($this->fields)->get($this->resource);
        });
    }
}
