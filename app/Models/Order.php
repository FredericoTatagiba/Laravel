<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;

class Order extends Model
{

    
    const STATUS_PENDING = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCELED = 2;
    const STATUS_REFUNDED = 3;

    const PAYMENT_CARD = 10;
    const PAYMENT_CASH = 11;
    const PAYMENT_PIX = 12;

    const STATUS_LABEL = [
        self::STATUS_PAID => "paid",
        self::STATUS_PENDING => "pending",
        self::STATUS_CANCELED => "canceled",
        self::STATUS_REFUNDED => "refunded",
        self::PAYMENT_CARD => "card",
        self::PAYMENT_CASH => "cash",
        self::PAYMENT_PIX => "pix",
     ];

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
