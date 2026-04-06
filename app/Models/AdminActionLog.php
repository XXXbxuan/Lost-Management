<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_name',
        'action_type',
        'target_name',
        'details',
    ];
}