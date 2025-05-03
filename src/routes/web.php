<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ItemLikeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\MyPage\MyPageController;
use App\Http\Controllers\MyPage\ProfileController;

// Fortify 認証画面
Fortify::loginView(fn() => view('auth.login'));
Fortify::registerView(fn() => view('auth.register'));

// メール認証ルート
Route::middleware('auth')->group(function () {
    Route::view('/email/verify', 'auth.verify-email')->name('verification.notice');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', '認証メールを再送信しました。');
    })->middleware('throttle:6,1')->name('verification.send');
});

// メール確認リンクアクセス時の処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return auth()->user()->profile_completed
        ? redirect('/')
        : redirect('/mypage/profile/create');
})->middleware(['auth','signed'])->name('verification.verify');

// -----------------------------------------------------------------------------
// 公開ページ（認証不要）
// -----------------------------------------------------------------------------
// 商品一覧（トップ画面） ※クエリtab=mylistでマイリスト表示
Route::get('/', [ItemController::class, 'index'])->name('items.index');
// タブ切替用（部分一覧取得）
Route::get('/items/switch-tab', [ItemController::class, 'switchTab'])->name('items.switchTab');

// 商品詳細
Route::get('/item/{item}', [ItemController::class, 'show'])->name('items.show');
// コメント投稿
Route::post('/item/{item}/comment', [CommentController::class, 'store'])->name('comments.store');

// -----------------------------------------------------------------------------
// 認証済み ＆ プロフィール完了済みユーザー向け
// -----------------------------------------------------------------------------
Route::middleware(['auth','verified.profile'])->group(function () {
    // 商品出品
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');
    // いいね
    Route::post('/items/{item}/toggle-like', [ItemLikeController::class, 'toggle'])->name('items.toggleLike');
    // 購入フロー
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // マイページ & プロフィール管理
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'store'])->name('mypage.profile.store');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.profile.update');
    Route::get('/mypage/profile/create', [ProfileController::class, 'create'])->name('mypage.profile.create');
    Route::get('/mypage/switch-tab',   [MyPageController::class, 'switchTab'])->name('mypage.switchTab');
});

// -----------------------------------------------------------------------------
// 決済結果ページ（認証不要）
// -----------------------------------------------------------------------------
Route::get('/success', [PurchaseController::class, 'handleSuccess'])->name('purchase.success');
Route::get('/cancel', function () {
    return redirect()->back();
})->name('purchase.cancel');
