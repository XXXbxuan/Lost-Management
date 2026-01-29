<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LostItemReport;
use App\Models\FoundItem;
use App\Models\MatchRecord;
use App\Models\Claim;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // 必须引入，用于生成 SMS 验证码

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
        $request->validate([
            'match_id' => 'required',
            'appointment_date' => 'required|date|after:now', // 必须是未来日期
            'appointment_time' => 'required',
        ]);

        $match = MatchRecord::findOrFail($request->match_id);

        // 合并日期和时间
        $fullDateTime = $request->appointment_date . ' ' . $request->appointment_time;

        // 生成 6 位随机 Token (用于模拟 SMS 链接)
        $token = strtoupper(Str::random(6));

        $match->update([
            'appointment_at' => $fullDateTime,
            'verification_token' => $token,
            'is_confirmed' => false, // 重置确认状态
        ]);

        return back()->with('success', 'Appointment scheduled successfully! SMS notification simulated.');
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