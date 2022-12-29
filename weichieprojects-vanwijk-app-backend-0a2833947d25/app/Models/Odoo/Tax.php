<?php

namespace App\Models\Odoo;

class Tax extends Base
{
    public $resource = 'account.tax';
    public $fields = [
        'id',
        'tax_group_id',
        'amount',
        'description',
    ];

    public function __construct($data=null)
    {
        $this->data = $data;
    }

    public function all(){
        return $this->cache('tax', function(){
            return $this->connect()->fields($this->fields)->get($this->resource);
        });
    }
}
