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
        "client_id", "delivery_address", "total_price", "status", "discount", "payment_method", "protocol"
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(Client::class);
    }   

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function setStatusPending(){
        $this->status = $this::STATUS_PENDING;
        $this->save();
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
