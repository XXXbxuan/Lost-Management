<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminActionLog;

class LogController extends Controller
{
    public function index()
    {
        $logs = AdminActionLog::latest()->paginate(20);

        return view('admin.logs.index', compact('logs'));
    }
}
