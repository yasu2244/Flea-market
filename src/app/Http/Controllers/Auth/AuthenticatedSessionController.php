<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function store(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (!$user->profile_completed) {
            return redirect('/mypage/profile/create');
        }

        return redirect('/');
    }
}
