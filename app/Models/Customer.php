<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'customerid';
    public $incrementing = false;
    public $timestamps = false;

    public function taxAddresses()
    {
        return $this->hasMany(TaxAddress::class, 'customerid', 'customerid');
    }
}
