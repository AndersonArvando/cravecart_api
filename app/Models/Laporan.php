<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laporan extends Model
{
    //
    use SoftDeletes;
    
    protected $table = 'laporan';

    public function transaksi(){
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }
}
