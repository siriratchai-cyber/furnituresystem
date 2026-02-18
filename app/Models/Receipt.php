<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $table = 'receipt'; // เพราะชื่อ table ไม่ใช่ receipts

    protected $primaryKey = 'receiptid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'receiptid',
        'orderid',
        'paymentmethod',
        'totalmoneyamount',
        'receivedmoneyamount',
        'changemoneyamount',
        'receipt_date',
    ];

    public $timestamps = true;
}
