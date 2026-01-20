<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminActionLog extends Model
{
    use HasFactory;

    /**
     * [关键修复]
     * $fillable 数组里的字段才允许被 create() 方法写入。
     * 这就是你报错的原因：之前没写这个白名单。
     */
    protected $fillable = [
        'admin_name',
        'action_type',
        'target_name',
        'details',
    ];
}
