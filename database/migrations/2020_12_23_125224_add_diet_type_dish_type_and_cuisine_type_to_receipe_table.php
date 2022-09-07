<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDietTypeDishTypeAndCuisineTypeToReceipeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receipe', function (Blueprint $table) {            
            $table->unsignedBigInteger('dish_type');
            $table->unsignedBigInteger('diet_type');
            $table->unsignedBigInteger('cuisine_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receipe', function (Blueprint $table) {
            //
        });
    }
}
