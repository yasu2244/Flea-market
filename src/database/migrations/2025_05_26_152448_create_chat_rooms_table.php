<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            // どの商品に対するチャットか
            $table->foreignId('item_id')
                  ->constrained()
                  ->onDelete('cascade');
            // 購入者
            $table->foreignId('buyer_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            // 出品者
            $table->foreignId('seller_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
