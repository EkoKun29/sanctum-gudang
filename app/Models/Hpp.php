<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hpp extends Model
{
    use HasFactory;
    protected $table = 'hpps';
    protected $fillable = [
        'tanggal',
        'nama_barang',
        'harga',
    ];
}
