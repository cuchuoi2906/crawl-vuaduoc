<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductPricePolicies;

class Products extends Model
{
    use HasFactory;
    protected $primaryKey = 'pro_id';

    protected $fillable = [
        'pro_name_vn',
        'pro_teaser_vn',
        'pro_price',
        'pro_code',
        'pro_discount_price'
    ];

    function productpricepolicies() 
    {
        return $this->hasMany(
            ProductPricePolicies::class, 
                'ppp_product_id', 
                'pro_id'
        );
    }
}
