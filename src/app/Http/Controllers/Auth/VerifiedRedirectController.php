<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifiedRedirectController
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        if (!$user->profile_completed) {
            return redirect('/profile/create');
        }

        return redirect('/');
    }
}
