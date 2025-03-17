<?php

namespace App\Providers;

use Laravel\Fortify\Fortify;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Requests\LoginRequest;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * サービスを登録
     */
    public function register()
    {
        parent::register();
    }

    /**
     * サービスのブート処理
     */
    public function boot()
    {
        // ユーザー作成、更新、リセットの設定
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // 認証ページの設定
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));

        RateLimiter::for('login', function () {
             return Limit::perMinute(20); // ログイン失敗は20回まで
        });

        // 登録後のリダイレクト先をプロフィール設定画面へ
        Fortify::redirects('register', function () {
            $user = Auth::user();
            return $user && !$user->profile_completed ? '/profile/create' : '/index';
        });

        // LoginRequestを適用
        Fortify::authenticateUsing(function ($request) {
            $validated = app(LoginRequest::class)->validated();

            if (!Auth::attempt($validated)) {
                throw ValidationException::withMessages([
                    'email' => 'ログイン情報が登録されていません',
                ]);
            }

            return Auth::user();
        });
    }
}
