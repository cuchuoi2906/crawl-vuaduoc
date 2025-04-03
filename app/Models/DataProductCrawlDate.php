<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductPricePolicies;

class DataProductCrawlDate extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    protected $table = 'data_product_crawl_date';

    protected $fillable = [
        'name',
        'id',
        'date'
    ];
    public $timestamps = false;

}
