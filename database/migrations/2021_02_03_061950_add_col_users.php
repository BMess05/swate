<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
             $table->string('cooking_days')->nullable();
             $table->time('breakfast_time')->nullable();
             $table->time('lunch_time')->nullable();
             $table->time('dinner_time')->nullable();
             
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
             $table->dropColumn(['cooking_days', 'breakfast_time','breakfast_time','lunch_time','dinner_time']);
        });
    }
}
