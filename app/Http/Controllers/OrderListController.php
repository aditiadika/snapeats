<?php

namespace App\Http\Controllers;

use App\Models\Table;

class OrderListController extends Controller
{
    public function show($qr_code)
    {
        $table = Table::where('qr_code', $qr_code)->firstOrFail();

        return view('order-list', compact('qr_code'));
    }
}
