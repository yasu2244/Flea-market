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

        // 認証処理
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // プロフィール登録の有無でリダイレクト先を分岐
        return $user->profile_completed
            ? redirect()->route('login')
            : redirect()->route('profile.create');
    }
}
