<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            // どのチャットルームのメッセージか
            $table->foreignId('chat_room_id')
                  ->constrained()
                  ->onDelete('cascade');
            // メッセージ送信ユーザー
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->text('body');
            // 画像パス
            $table->string('image_path')->nullable();
            // 既読したか
            $table->unsignedBigInteger('read_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};

