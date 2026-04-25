<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED (PK)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // FK
            $table->string('name');
            $table->string('brand')->nullable();
            $table->unsignedInteger('price');
            $table->text('description');
            $table->string('image_path');
            $table->string('condition', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
