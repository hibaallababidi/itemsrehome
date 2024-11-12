<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'sub_category_id',
        'location_id',
        'product_name',
        'description',
        'duration_of_use',
        'phone_number',
        'product_status',
        'is_sold',
        'items_count',
        'is_free',
        'is_deliverable',
        'price_suggestion',
    ];

    public function photos()
    {
        return $this->hasMany(ProductPhoto::class, 'product_id', 'id');
    }

    public function socialLinks()
    {
        return $this->hasMany(ProductSocialLink::class, 'product_id', 'id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class, 'product_id', 'id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
    }
}
