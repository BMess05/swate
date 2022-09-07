<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CookingLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cooking_levels')->insert([
            'level_name' => 'Beginner',
            'description' => 'I can fry an egg?'
        ]);

        DB::table('cooking_levels')->insert([
            'level_name' => 'Intermediate',
            'description' => 'I have a few recipes up my sleeve'
        ]);

        DB::table('cooking_levels')->insert([
            'level_name' => 'Advanced',
            'description' => 'I like to experiment'
        ]);
    }
}
