<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('payment_method');
            $table->string('postal_code');
            $table->string('address');
            $table->string('building');
            $table->unsignedInteger('price');
            $table->boolean('is_completed')->default(false);
            $table->string('stripe_session_id')->nullable();
            // 購入者が評価済みか
            $table->boolean('buyer_rated')->default(false);
            // 出品者が評価済みか
            $table->boolean('seller_rated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
