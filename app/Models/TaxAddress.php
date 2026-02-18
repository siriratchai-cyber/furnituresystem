<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxAddress extends Model
{
    protected $table = 'tax_address';
    protected $primaryKey = 'taxaddressid';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerid', 'customerid');
    }
}


