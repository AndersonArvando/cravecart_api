<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiDetail extends Model
{
    //
    use SoftDeletes;
    
    protected $table = 'transaksi_detail';

    public function makanan() {
        return $this->belongsTo(Makanan::class, 'makanan_id');
    }
}
