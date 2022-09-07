<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->nullable();
            $table->integer('goals')->default(0);
            $table->integer('diet')->default(0);
            $table->integer('cooking_level')->default(0);
            $table->string('allergies')->nullable();
            $table->integer('people_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'goals', 'diet', 'cooking_level', 'allergies', 'people_count']);
        });
    }
}
