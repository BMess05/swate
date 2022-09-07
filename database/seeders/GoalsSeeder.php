<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class GoalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('goals')->insert([
            'goal_name' => 'Save money'
        ]);

        DB::table('goals')->insert([
            'goal_name' => 'Save the planet'
        ]);

        DB::table('goals')->insert([
            'goal_name' => 'Learn to cook'
        ]);

        DB::table('goals')->insert([
            'goal_name' => 'Discover new recipes'
        ]);
    }
}
