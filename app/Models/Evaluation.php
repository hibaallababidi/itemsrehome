<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable=[
      'evaluator_id',
      'evaluated_id',
      'evaluation_number'
    ];
}
