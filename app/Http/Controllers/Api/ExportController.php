<?php

namespace App\Http\Controllers\Api; // Pastikan namespace ini tepat

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function pdf()
    {
        return response()->json(['message' => 'Export PDF logic here']);
    }

    public function excel()
    {
        return response()->json(['message' => 'Export Excel logic here']);
    }
}