<?php

namespace App\Http\Controllers;

use App\Models\HargaKonsumen;
use Illuminate\Http\Request;

class HargaKonsumenController extends Controller
{
    public function index(Request $request)
{
    $limit = $request->input('limit', 10);
    $offset = $request->input('offset', 0);
    $search = $request->input('search');

    $query = HargaKonsumen::query();

    if ($search) {
        $keywords = explode(' ', $search); // pisahkan kata berdasarkan spasi

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $word) {
                $q->whereRaw("CONCAT_WS(' ', sales, tanggal, nama_konsumen, nama_barang, harga_jual) LIKE ?", ["%$word%"]);
            }
        });

        // ✅ Jika ada pencarian, ambil semua hasil tanpa limit
        $data = $query->orderBy('created_at', 'desc')->get();
    } else {
        // ✅ Kalau tidak ada pencarian, pakai pagination
        $data = $query->orderBy('created_at', 'desc')
                      ->offset($offset)
                      ->limit($limit)
                      ->get();
    }

    $total = $query->count();

    return response()->json([
        'data' => $data,
        'offset' => (int) $offset,
        'limit' => (int) $limit,
        'total' => $total,
    ]);
}



}
