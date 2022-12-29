<?php

namespace App\Models;

use App\Services\ProductService;
use Carbon\Carbon;
use App\Enums\RequestType;
use App\Models\Odoo\Product;
use Illuminate\Database\Eloquent\Model;
use App\Models\Request as VanWijkRequest;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Extension extends Model
{
    use HasFactory;
    public $fillable = [
        'product_id',
        'reason',
    ];
    protected $appends = ['product_attributes'];

    public function request(): MorphOne
    {
        return $this->morphOne(VanWijkRequest::class, 'requestable');
    }

    public function getProductAttributesAttribute(){
        return (new ProductService())->attributesOf($this->attributes['product_id']);
    }

}
