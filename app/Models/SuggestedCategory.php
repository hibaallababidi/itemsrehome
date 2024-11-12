<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuggestedCategory extends Model
{
    use HasFactory;

    protected $fillable=[
      'user_id',
      'suggested_category_name'
    ];
}
