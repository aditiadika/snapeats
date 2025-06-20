<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function show($qr_code)
    {
        $table = Table::where('qr_code', $qr_code)->firstOrFail();
        return view('menu', compact('qr_code'));
    }
}
