<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LostItemReport;
use App\Models\FoundItem;
use App\Models\MatchRecord;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// 👇👇👇 必须加上这两行！！ 👇👇👇
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentConfirmation;

class ClaimController extends Controller
{
    // 1. 显示流程页面 (Timeline & Process View)
    public function createClaim(Request $request)
    {
        // 获取 URL 里的 lost_id
        $lostId = $request->query('lost_id');

        // 找到报失单
        $lostReport = LostItemReport::findOrFail($lostId);
        
        // 必须是 "Verified" 的匹配记录才能进入流程
        $match = MatchRecord::where('lostId', $lostReport->id)
                            ->where('status', 'Verified')
                            ->firstOrFail(); 

        // 找到对应的拾获物品
        $foundItem = FoundItem::findOrFail($match->foundId);

        // 👉 指向新的流程视图 'staff.claims.process'
        return view('staff.claims.process', compact('lostReport', 'foundItem', 'match'));
    }

    // 2. 保存预约时间 (新功能 - Step 1)
    public function schedule(Request $request)
    {
        // 1. 验证输入
        $request->validate([
            'match_id' => 'required',
            'appointment_date' => 'required|date|after:now', // 必须是未来日期
            'appointment_time' => 'required',
        ]);

        $match = MatchRecord::findOrFail($request->match_id);

        // 2. 合并日期和时间
        $fullDateTime = $request->appointment_date . ' ' . $request->appointment_time;

        // 3. 生成 6 位随机 Token
        $token = strtoupper(Str::random(6));

        // 4. 更新数据库
        $match->update([
            'appointment_at' => $fullDateTime,
            'verification_token' => $token,
            'is_confirmed' => false, // 重置确认状态
        ]);

        // 🔥🔥🔥 5. 发送真实邮件 (新增部分) 🔥🔥🔥
        
        // 生成给用户点击的链接 (会自动带上你的 IP)
        $link = route('pickup.confirm', ['token' => $token]);
        
        // 👇 这里填你手机上能收到的邮箱 (为了测试先发给你自己)
        $passengerEmail = 'chiabx-wp22@student.tarc.edu.my'; 
        
        // 发送动作
        Mail::to($passengerEmail)->send(new AppointmentConfirmation($match, $link));

        // 6. 返回成功信息
        return back()->with('success', 'Appointment set! Email sent to ' . $passengerEmail);
    }
    // 3. 最终交接 (核心逻辑 - Step 2)
    public function store(Request $request)
    {
        $request->validate([
            'lostId' => 'required',
            'foundId' => 'required',
            'claimerName' => 'required',
            'claimerIcPassport' => 'required',
            'claimerPhone' => 'required',
        ]);

        // 🔥 安全检查：必须先由用户确认预约，才能进行交接
        // 重新获取最新的 match 状态
        $match = MatchRecord::where('lostId', $request->lostId)
                            ->where('foundId', $request->foundId)
                            ->firstOrFail();

        if (!$match->is_confirmed) {
            return back()->withErrors(['claimerIcPassport' => 'Error: The passenger has NOT confirmed the appointment via SMS link yet. Cannot proceed.']);
        }

        // 开启数据库事务
        DB::transaction(function () use ($request) {
            // A. 创建认领记录
            Claim::create([
                'lostId' => $request->lostId,
                'foundId' => $request->foundId,
                'claimerName' => $request->claimerName,
                'claimerIcPassport' => $request->claimerIcPassport,
                'claimerPhone' => $request->claimerPhone,
                'processedBy' => auth()->id(),
                'claimedAt' => now(),
            ]);

            // B. 更新报失单状态 -> Claimed
            LostItemReport::where('id', $request->lostId)->update(['status' => 'Claimed']);

            // C. 更新物品状态 -> Claimed (自动释放库存 Slot)
            FoundItem::where('id', $request->foundId)->update(['status' => 'Claimed']);
        });

        return redirect()->route('staff.lost-items.index')
                         ->with('success', 'Handover completed! Item released from inventory.');
    }

    // 4. 显示历史记录列表 (Audit Log)
    public function index()
    {
        $claims = Claim::with(['foundItem', 'lostReport', 'handler'])
            ->latest('claimedAt')
            ->paginate(10);

        return view('staff.claims.index', compact('claims'));
    }
}