<?php

namespace App\Http\Controllers;

use App\Models\Hpp;
use Illuminate\Http\Request;

class HppController extends Controller
{
    public function index()
    {
        $hppData = Hpp::orderBy('tanggal', 'desc')->get();

        return response()->json($hppData);
    }
}
