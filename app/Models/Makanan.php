<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Makanan extends Model
{
    //
    use SoftDeletes;
    
    protected $table = 'makanan';
}
