<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureVerifiedAndProfile
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // メール未認証なら /email/verify へ
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // プロフィール未作成かつプロフィール作成ページでないならリダイレクト
            if (
                !$user->profile_completed &&
                !$request->is('mypage/profile/create') &&
                !$request->is('mypage/profile') // POST送信時
            ) {
                return redirect('/mypage/profile/create');
            }
        }

        return $next($request);
    }
}
