<?php

namespace App\Models;

use App\Models\Request as VanWijkRequest;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Deregister extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['product_data_array', 'product_attributes'];
    /*protected $casts = [
        'product_data' => 'array',
    ];*/

    public function request(): MorphOne
    {
        return $this->morphOne(VanWijkRequest::class, 'requestable');
    }

    public function getProductAttributesAttribute(){
        $product_data = $this->getProductDataArrayAttribute();
        return (new ProductService())->attributesOf($product_data['product_id'][0]);
    }
    public function getProductDataArrayAttribute(){
        return json_decode($this->attributes['product_data'], true);
    }

}
