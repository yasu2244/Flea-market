<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return back()->with('status', 'すでにメール認証が完了しています。');
        }

        Auth::user()->sendEmailVerificationNotification();

        return back()->with('status', '認証メールを再送信しました。');
    }
}
