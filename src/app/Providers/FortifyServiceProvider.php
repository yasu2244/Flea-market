<?php

namespace App\Providers;

use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Actions\Fortify\CreateNewUser;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->email.$request->ip());
        });

        // 登録後：ログインさせずメール認証画面へ
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('verification.notice');
                }
            };
        });

        // メール認証ページ表示
        $this->app->singleton(VerifyEmailViewResponse::class, function () {
            return new class implements VerifyEmailViewResponse {
                public function toResponse($request)
                {
                    return view('auth.verify-email');
                }
            };
        });

        // 認証処理（メール認証未完了ならログイン不可）
        Fortify::authenticateUsing(function (Request $request) {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (!Auth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => 'ログイン情報が正しくありません。',
                ]);
            }

            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                Auth::logout(); // 認証完了前のログインを防ぐ
                throw ValidationException::withMessages([
                    'email' => 'メール認証が完了していません。メールをご確認ください。',
                ]);
            }

            return $user;
        });
    }
}
