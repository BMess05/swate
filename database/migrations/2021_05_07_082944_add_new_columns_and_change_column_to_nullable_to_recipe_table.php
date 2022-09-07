<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewColumnsAndChangeColumnToNullableToRecipeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receipe', function (Blueprint $table) {
            $table->unsignedBigInteger('dish_type')->nullable()->change();
            $table->unsignedBigInteger('diet_type')->nullable()->change();
            $table->unsignedBigInteger('cuisine_type')->nullable()->change();
            $table->text('directions')->change();
            $table->text('receipe_image')->change();
            $table->text('tags')->nullable();
            $table->string('author')->nullable();
            $table->string('author_profile')->nullable();
            $table->string('serving')->nullable();
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
