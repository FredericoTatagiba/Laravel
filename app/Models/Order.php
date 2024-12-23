<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Order extends Model
{

    const STATUS_PAID = "paid";
    const STATUS_PENDING = "pending";
    const STATUS_CANCELED = "canceled";

    // const STATUS_LABEL = [
    //      self::STATUS_PAID => "Pago",
    //  ];

    protected $fillable = [
        "delivery_address", "total_price", "status", "discount",
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(Client::class);
    }   

    public function setStatusPaid(){
         $this->status = $this::STATUS_PAID;
         $this->save();
    }

    public function setStatusCanceled(){
        $this->status = $this::STATUS_CANCELED;
        $this->save();
    }


}
