<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPricePolicies extends Model
{
    use HasFactory;
    protected $primaryKey = 'ppp_id';

    protected $fillable = [
        'ppp_product_id',
        'ppp_quantity',
        'ppp_price'
    ];
}
