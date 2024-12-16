<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        "name", "stock","price"
    ] ;

    public function orders(){
        return $this->belongsToMany(Order::class,"order_products","product_id","order_id")->withPivot('quantity');
    }
}
