<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            // ユーザーテーブルとのリレーション (FK)
            // 'constrained()' でusersテーブルのidを参照します
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');             // 氏名
            $table->string('postal_code', 8);   // 郵便番号
            $table->string('address');          // 住所
            $table->string('building')->nullable(); // 建物名 (NULL許容)
            $table->string('image_path')->nullable(); // 画像パス (NULL許容)
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
        Schema::dropIfExists('profiles');
    }
}
