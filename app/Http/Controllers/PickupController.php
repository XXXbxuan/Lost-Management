<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MatchRecord;
use Carbon\Carbon;

class PickupController extends Controller
{
    // 1. 显示确认页面 (乘客点击链接后看到的界面)
    public function showConfirmationPage($token)
    {
        // 根据 Token 找记录
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        // 如果已经确认过了，直接显示成功页
        if ($match->is_confirmed) {
            return view('pickup.success');
        }

        return view('pickup.confirm', compact('match'));
    }

    // 2. 处理确认动作 (乘客点击 "YES" 按钮)
    public function processConfirmation($token)
    {
        $match = MatchRecord::where('verification_token', $token)->firstOrFail();

        $match->update([
            'is_confirmed' => true,
            'confirmed_at' => now(),
        ]);

        return view('pickup.success');
    }
}