<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
      // ここで作成したシーダーを呼び出す
        $this->call([
            UserSeeder::class,      // 先にユーザーを作って...
            CategorySeeder::class,
            ItemSeeder::class,
        ]);
    }
}
