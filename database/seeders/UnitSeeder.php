<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('units')->insert([
            'unit' => 'kg',
        ]);

       DB::table('units')->insert([
            'unit' => 'gram',
        ]);
        DB::table('units')->insert([
            'unit' => 'mL',
        ]);
         DB::table('units')->insert([
            'unit' => 'litres',
        ]);
          DB::table('units')->insert([
            'unit' => 'cup',
        ]);

        DB::table('units')->insert([
            'unit' => 'pieces',
        ]);

        DB::table('units')->insert([
            'unit' => 'spoon',
        ]);

        DB::table('units')->insert([
            'unit' => 'table spoon',
        ]);

        DB::table('units')->insert([
            'unit' => 'pack',
        ]);
        DB::table('units')->insert([
            'unit' => 'slice',
        ]);
    }
}
