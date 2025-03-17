<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        return view('profile.create', compact('profile'));
    }

    public function store(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        $user = Auth::user();

        // 既存のプロフィールを取得または新規作成
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        // 名前・住所関連のデータを更新
        $profile->fill($addressRequest->validated());

        // プロフィール画像の更新（画像がアップロードされた場合のみ）
        if ($profileRequest->hasFile('profile_image')) {
            $path = $profileRequest->file('profile_image')->store('profile_images', 'public');
            $profile->profile_image = $path;
        }

        $profile->save();

        // profile_completed を true に更新
        $user->update(['profile_completed' => true]);

        return redirect('/profile')->with('status', 'プロフィールが作成されました');
    }


    // プロフィール閲覧ページ
    public function show()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile.show', compact('profile'));
    }

    // プロフィール編集ページを表示
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('profile.edit', compact('profile'));
    }

    // プロフィール情報を更新
    public function update(AddressRequest $addressRequest, ProfileRequest $profileRequest)
    {
        $user = Auth::user();
        $profile = $user->profile;

        // 名前・住所関連の更新
        $profile->fill($addressRequest->validated());

        // プロフィール画像の更新
        if ($profileRequest->hasFile('profile_image')) {
            if ($profile->profile_image) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $path = $profileRequest->file('profile_image')->store('profile_images', 'public');
            $profile->profile_image = $path;
        }

        $profile->save();

        return redirect('/profile')->with('status', 'プロフィールが更新されました');
    }
}

