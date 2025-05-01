<?php

namespace App\Providers;

use Laravel\Fortify\Fortify;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use App\Http\Requests\LoginRequest;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Fortify の各種カスタマイズ設定
     */
    public function boot()
    {
        // ログイン試行回数の制限
        RateLimiter::for('login', function () {
            return Limit::perMinute(10);
        });

        // ユーザー登録・情報更新・パスワード更新等の設定
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // 認証ビューの設定
        Fortify::loginView(fn (Request $request) => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));

        // メール認証ビューのレスポンス登録
        $this->app->singleton(VerifyEmailViewResponse::class, function () {
            return new class implements VerifyEmailViewResponse {
                public function toResponse($request)
                {
                    return view('auth.verify-email');
                }
            };
        });

        // 登録後のリダイレクト先（メール認証画面へ）
        Fortify::redirects('register', function () {
            return '/email/verify';
        });

        // ログイン時の処理（バリデーション + 認証 + メール確認）
        Fortify::authenticateUsing(function (Request $request) {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (!Auth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません。',
                ]);
            }

            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'メール認証が完了していません。メールをご確認ください。',
                ]);
            }

            return $user;
        });
    }
}
