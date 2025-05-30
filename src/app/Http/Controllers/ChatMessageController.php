<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Http\Requests\ChatMessageRequest;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
    /**
     * メッセージ投稿
     */
    public function store(ChatMessageRequest $request, ChatRoom $chatRoom)
    {
        $data = $request->validated();
        $data['chat_room_id'] = $chatRoom->id;
        $data['user_id']      = auth()->id();

        // 画像が選択されていれば storage/app/public/chat_images に保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('chat_images', 'public');
            $data['image_path'] = $path;
        }

        $msg = ChatMessage::create($data);

        return back();
    }

    /**
     * メッセージ編集フォーム表示
     */
    public function edit(ChatRoom $chatRoom, ChatMessage $chatMessage)
    {
        // 他ユーザーの編集を防ぐ
        $this->authorize('update', $chatMessage);

        return view('chat.edit', compact('chatRoom', 'chatMessage'));
    }

    /**
     * メッセージ更新
     */
    public function update(ChatMessageRequest $request, ChatRoom $chatRoom, ChatMessage $chatMessage)
    {
        $this->authorize('update', $chatMessage);

        try {
            $chatMessage->update($request->only('body'));
            return response()->json([
                'body' => nl2br(e($chatMessage->body))
            ], 200);
        } catch (\Exception $e) {
            // エラー詳細をログにも出して
            \Log::error($e);
            // こちらを JSON で返却
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * メッセージ削除
     */
    public function destroy(ChatRoom $chatRoom, ChatMessage $chatMessage)
    {
        $this->authorize('delete', $chatMessage);
        $chatMessage->delete();

        return redirect()
            ->route('chat_rooms.show', $chatRoom)
            ->with('status', 'メッセージを削除しました。');
    }
}
