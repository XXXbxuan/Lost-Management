<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActionLog;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(): View
    {
        $logs = AdminActionLog::latest()->paginate(20);

        return view('admin.logs.index', compact('logs'));
    }
}