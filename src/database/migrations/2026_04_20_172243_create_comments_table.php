<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            // 誰がコメントしたか
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // どの商品へのコメントか
            $table->foreignId('item_id')->constrained()->onDelete('cascade');

            // コメント内容 (長文も考慮してtext型)
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
}
