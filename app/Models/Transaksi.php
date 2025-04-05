<?php

namespace App\Models;

use App\Http\Middleware\Mahasiswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    //
    use SoftDeletes;
    
    protected $table = 'transaksi';

    public function detail() {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    public function mahasiswa() {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function kantin() {
        return $this->belongsTo(User::class, 'kantin_id');
    }
}
