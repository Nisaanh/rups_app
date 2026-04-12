<?php

namespace App\Http\Controllers\Api; // Pastikan ada sub-folder \Api

use App\Http\Controllers\Controller;
use App\Models\Keputusan;
use Illuminate\Http\Request;

class KeputusanApiController extends Controller
{
    public function index()
    {
        return response()->json(Keputusan::all());
    }
}