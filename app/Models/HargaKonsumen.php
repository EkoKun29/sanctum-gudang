<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaKonsumen extends Model
{
    use HasFactory;
    protected $table = 'harga_konsumens';
    protected $fillable = [
        'sales',
        'tanggal',
        'nama_konsumen',
        'nama_barang',
        'harga_jual',
    ];
}
