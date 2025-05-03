<?php

namespace App\Http\Controllers\MyPage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $profile = $user->profile ?? new Profile(); // プロフィールがなければ新規作成

        return view('mypage.profile.create', compact('profile'));
    }

    public function store(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        $user = Auth::user();

        // 既存のプロフィールを取得または新規作成
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        // 名前・住所関連のデータを更新
        $profile->fill($addressRequest->validated());

        // プロフィール画像の登録（画像がアップロードされた場合のみ）
        if ($profileRequest->hasFile('profile_image')) {
            $path = $profileRequest->file('profile_image')->store('profile_images', 'public');
            $profile->profile_image = $path;
        }

        $profile->save();

        // profile_completed を true に更新
        $user->update(['profile_completed' => true]);

        return redirect('/')->with('status', 'プロフィールが作成されました');
    }

    // プロフィール編集ページを表示
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('mypage.profile.edit', compact('profile'));
    }

    // プロフィール情報を更新
    public function update(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        $profile = Auth::user()->profile;

        // ① 名前/郵便番号/住所/建物 のみをまず取得
        $data = $addressRequest->validated();

        // ② 画像があれば storage に保存してパスを $data に上書き追加
        if ($profileRequest->hasFile('profile_image')) {
            // 既存ファイルを消す
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            // storage/app/public/profile_images に保存
            $data['profile_image'] = $profileRequest
                ->file('profile_image')
                ->store('profile_images', 'public');
        }

        $profile->update($data);

        return redirect()
            ->route('items.index')
            ->with('status','プロフィールを更新しました');
    }


    public function editAddress()
    {
        $user = Auth::user();
        return view('purchase.address_edit', compact('user'));
    }

    public function updateAddress(Request $request)
    {
        $validated = $request->validate([
            'postal_code' => ['required', 'string'],
            'address' => ['required', 'string'],
            'building' => ['nullable', 'string'],
        ]);

        session()->put('purchase_address', $validated);

        return redirect()->route('purchase.show', session('purchase_item_id')); // 戻る
    }
}

