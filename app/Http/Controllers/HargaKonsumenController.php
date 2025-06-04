<?php

namespace App\Http\Controllers;

use App\Models\HargaKonsumen;
use Illuminate\Http\Request;

class HargaKonsumenController extends Controller
{
    public function index(){
        $hargaKonsumen = HargaKonsumen::orderBy('created_at', 'desc')->get();
        return response()->json($hargaKonsumen);
    }
}
