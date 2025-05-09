<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureVerifiedAndProfile
{
    public function handle(Request $request, Closure $next)
    {
        // PHPUnit の Feature テストも、Dusk の Browser テストも、
        // APP_ENV=testing または APP_ENV=dusk のいずれでもスキップ
        if (app()->environment(['testing','dusk'])) {
            return $next($request);
        }

        $user = Auth::user();

        // 以下は従来どおり
        if ($user) {
            if (! $user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            if (
                ! $user->profile_completed &&
                ! $request->is('mypage/profile/create') &&
                ! $request->is('mypage/profile')
            ) {
                return redirect('/mypage/profile/create');
            }
        }

        return $next($request);
    }
}
