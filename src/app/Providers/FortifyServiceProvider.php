<?php

namespace App\Providers;

use Laravel\Fortify\Fortify;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * サービスのブート処理
     */
    public function boot()
    {
        // ログイン試行回数の制限を先に適用
        RateLimiter::for('login', function () {
            return Limit::perMinute(20); // ログイン失敗は20回まで
        });

        // ユーザー作成、更新、リセットの設定
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // 認証ページの設定
        Fortify::loginView(function (Request $request) {
            return view('auth.login');
        });

        Fortify::registerView(fn () => view('auth.register'));

        // 登録後のリダイレクト先をプロフィール設定画面へ
        Fortify::redirects('register', function () {
            $user = Auth::user();
            return $user && !$user->profile_completed ? '/profile/create' : '/';
        });

    }
}
