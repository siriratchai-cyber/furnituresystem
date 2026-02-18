<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxInvoice extends Model
{
    protected $table = 'tax_invoice';
    protected $primaryKey = 'invoiceid';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'invoiceid',
        'invoicenumber',
        'invoicedate',
        'branchcode',
        'taxaddressid',
        'receiptid'
    ];
}
