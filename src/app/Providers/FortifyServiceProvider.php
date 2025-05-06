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
use App\Http\Requests\LoginRequest;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->email.$request->ip());
        });

        // 登録後：ログイン画面へ
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    Auth::logout(); // 明示的にログアウトさせる
                    return redirect()->route('login')->with('status', '登録が完了しました。ログインしてメール認証を行ってください。');
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

        // 認証処理
        Fortify::authenticateUsing(function (Request $request) {
            // FormRequest をインスタンス化してバリデーション
            $loginRequest = app(LoginRequest::class);
            $loginRequest->merge($request->only(['email', 'password']));
            $validated = $loginRequest->validated();

            if (!Auth::attempt($validated)) {
                throw ValidationException::withMessages([
                    'email' => 'ログイン情報が正しくありません。',
                ]);
            }

            return Auth::user();
        });
    }
}
