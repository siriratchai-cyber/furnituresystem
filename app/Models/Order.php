<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'orderid';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function taxAddress()
{
    return $this->belongsTo(
        TaxAddress::class,
        'tax_address_id',   // foreign key ใน orders
        'taxaddressid'      // primary key ใน tax_address
    );
}

}
