<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatRoomController extends Controller
{
    public function show($chatRoom)
    {
        // ダミーで画面遷移だけ確認する
        return view('chat.show', compact('chatRoom'));
    }
}
