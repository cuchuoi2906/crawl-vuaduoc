<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductPricePolicies;

class DataProductCrawl extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $table = 'data_product_crawl';

    protected $fillable = [
        'name',
        'id',
        'price_vuaduoc',
        'price_discount',
        'price_policies',
        'price_crawl',
        'price_difference',
        'status',
        'price_sale_crawl_quality_2'
    ];
    public $timestamps = false;

}
