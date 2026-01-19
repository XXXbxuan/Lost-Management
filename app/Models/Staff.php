<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // [Pro级细节]

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'staff_id'; // 告诉 Laravel 主键不是 id 而是 staff_id

    protected $fillable = [
        'user_id',
        'name',
        'contact_number',
        'status',
        'department'
    ];

    // 定义反向关系：一个 Staff 属于一个 User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}