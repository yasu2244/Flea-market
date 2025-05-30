<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            // どの購入(取引)に紐づくか
            $table->foreignId('purchase_id')
                ->constrained()
                ->onDelete('cascade');
            // 評価をつける側
            $table->foreignId('rater_id')
                ->constrained('users')
                ->onDelete('cascade');
            // 評価される側
            $table->foreignId('ratee_id')
                ->constrained('users')
                ->onDelete('cascade');
            // 1～5 の星評価
            $table->unsignedTinyInteger('rating');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
