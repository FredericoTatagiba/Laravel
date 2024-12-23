<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    const STATUS_PAID = "paid";
    const STATUS_PENDING = "pending";
    const STATUS_CANCELED = "canceled";

    // const STATUS_LABEL = [
    //      self::STATUS_PAID => "Pago",
    //  ];

    protected $fillable = [
        "delivery_address", "total_price", "discount", "status",
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products', 'order_id', 'product_id')
            ->withPivot('quantity');
    }

    public function setStatusPaid(){
         $this->status = $this::STATUS_PAID;
         $this->save();
     }


}
