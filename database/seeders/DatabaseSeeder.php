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
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call(UsersTableSeeder::class);// 用户填充
        $this->call(TopicsTableSeeder::class);// 话题填充
		$this->call(RepliesTableSeeder::class);// 回复填充
		$this->call(LinksTableSeeder::class);// 推荐资源填充

    }
}
