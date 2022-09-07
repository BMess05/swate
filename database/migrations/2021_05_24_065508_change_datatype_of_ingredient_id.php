<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatatypeOfIngredientId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      
        Schema::table('receipe_ingredients', function (Blueprint $table) {
            $table->dropColumn(['ingredient_id']);
        });
    
        Schema::table('receipe_ingredients', function (Blueprint $table) {
            $table->string('ingredient_name', 255)->after('receipe_id')->nullable();
          
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('receipe_ingredients', function (Blueprint $table) {
            //
        });
    }
}
