<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // 如果用户没登录，或者登录了但角色不是 Admin
        if (!auth()->check() || auth()->user()->role !== 'Admin') {
            // 直接踢回首页，或者显示 403 禁止访问
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
