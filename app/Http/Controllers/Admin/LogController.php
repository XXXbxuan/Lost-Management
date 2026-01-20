<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminActionLog; // 引用模型

class LogController extends Controller
{
    public function index()
    {
        // 获取所有日志，最新的在最上面，每页显示 20 条
        $logs = AdminActionLog::latest()->paginate(20);

        return view('admin.logs.index', compact('logs'));
    }
}
