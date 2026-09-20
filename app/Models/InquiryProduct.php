<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryProduct extends Model
{
    use HasFactory;

    protected $table = 'inquiries_has_products';
    protected $fillable = [
        // 'id',
        'inquiry_id',
        'main_category_id',
        'sub_category_id',
        'product_type_id',
        'brand_id',
        'specification_id',
        'packaging_id',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class, 'inquiry_id', 'id');
    }

    public function product_type()
    {
        return $this->belongsTo(Product_Type::class, 'product_type_id', 'id');
    }
}
