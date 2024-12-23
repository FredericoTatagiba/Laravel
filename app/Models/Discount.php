<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'price', 'discount'
    ] ;
}
