<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfProfileIncomplete
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->hasVerifiedEmail() && !$user->profile_completed && !$request->is('mypage/profile/create')) {
            return redirect()->route('mypage.profile.create');
        }

        return $next($request);
    }
}
