<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemLikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MyPage\MyPageController;
use App\Http\Controllers\MyPage\ProfileController;
use App\Http\Controllers\Auth\EmailVerificationController;


// Fortify 認証画面
Fortify::loginView(fn()    => view('auth.login'));
Fortify::registerView(fn() => view('auth.register'));

// メール認証ルート
Route::middleware('auth')->group(function () {
    // 認証通知画面
    Route::view('/email/verify', 'auth.verify-email')
         ->name('verification.notice');

    // 再送信（6回/分制限）
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', '認証メールを再送信しました。');
    })
    ->middleware('throttle:6,1')
    ->name('verification.send');
});

// 認証リンクアクセス時の処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return auth()->user()->profile_completed
        ? redirect('/')
        : redirect('/mypage/profile/create');
})
->middleware(['auth','signed'])
->name('verification.verify');

// 公開ページ（認証不要）
Route::get('/',               [ItemController::class, 'index'])
     ->name('items.index');
Route::get('/items/switch-tab', [ItemController::class, 'switchTab'])
     ->name('items.switchTab');
Route::get('/item/{item}',    [ItemController::class, 'show'])
     ->name('items.show');
Route::post('/item/{item}/comment', [CommentController::class, 'store'])
     ->name('comments.store');


// 認証済み ＆ プロファイル完了済みユーザー向け
Route::middleware(['auth','verified.profile'])->group(function () {

    // — 商品出品 —
    Route::prefix('sell')->name('sell.')->controller(ItemController::class)->group(function(){
        Route::get('/',  'create')->name('create');
        Route::post('/', 'store')->name('store');
    });

    // — いいね機能 —
    Route::post('/items/{item}/toggle-like',
        [ItemLikeController::class, 'toggle']
    )->name('items.toggleLike');

    // — 購入フロー —
    Route::prefix('purchase')->name('purchase.')->controller(PurchaseController::class)->group(function(){
        Route::get('/{item}',         'show')->name('show');
        Route::post('/{item}',        'store')->name('store');
        Route::get('/address/{item}', 'editAddress')->name('address.edit');
        Route::post('/address/{item}','updateAddress')->name('address.update');
    });

    // — マイページ & プロフィール管理 —
    Route::prefix('mypage')->name('mypage.')->group(function(){
        // マイページ本体
        Route::get('/',            [MyPageController::class, 'index'])->name('index');
        Route::get('switch-tab',   [MyPageController::class, 'switchTab'])->name('switchTab');

        // プロフィール作成／編集
        Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function(){
            Route::get('create', 'create')->name('create');
            Route::post('',      'store')->name('store');
            Route::get('',       'edit')->name('edit');
            Route::put('',       'update')->name('update');
        });
    });
});

// 決済結果ページ（認証不要）
Route::get('/success', [PurchaseController::class, 'handleSuccess'])
     ->name('purchase.success');

Route::get('/cancel', function () {
    return redirect()->back();
})->name('purchase.cancel');
