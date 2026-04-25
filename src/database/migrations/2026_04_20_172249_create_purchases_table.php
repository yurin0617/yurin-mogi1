<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            // 購入者
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // 商品ID（ユニーク制約を追加）
            // これにより、同じ商品が複数の購入履歴に紐付くことを防ぎます
            $table->foreignId('item_id')->unique()->constrained()->onDelete('cascade');
            // 購入情報および配送情報
            $table->string('payment_method', 50);      // 決済方法
            $table->string('shipping_postal_code', 8); // 配送先郵便番号
            $table->string('shipping_address');        // 配送先住所
            $table->string('shipping_building')->nullable(); // 配送先建物名

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
        Schema::dropIfExists('purchases');
    }
}
