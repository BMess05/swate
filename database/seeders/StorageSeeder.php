<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class StorageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('storage_types')->insert([
            'storage_name' => 'Fridge'
        ]);

        DB::table('storage_types')->insert([
            'storage_name' => 'Freezer'
        ]);

        DB::table('storage_types')->insert([
            'storage_name' => 'Pantry'
        ]);
    }
}
