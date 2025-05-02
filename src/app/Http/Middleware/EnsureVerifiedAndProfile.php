<?php

// App\Http\Middleware\EnsureVerifiedAndProfileComplete.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureVerifiedAndProfile
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (!$user->profile_completed && !$request->is('mypage/profile/create')) {
            return redirect('/mypage/profile/create');
        }

        return $next($request);
    }
}

