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
        //$this->call(DietsSeeder::class);
        $this->call(StorageSeeder::class);
        $this->call(GoalsSeeder::class);
        $this->call(CookingLevelsSeeder::class);
         //$this->call(UnitSeeder::class);
    }
}
