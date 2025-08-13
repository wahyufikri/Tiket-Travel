<?php

namespace App\Http\Controllers;

use App\Models\MidtransLog;

class MidtransLogController extends Controller
{
    public function index()
    {
        $logs = MidtransLog::latest()->paginate(20);
        return view('midtrans_logs.index', compact('logs'));
    }
}

