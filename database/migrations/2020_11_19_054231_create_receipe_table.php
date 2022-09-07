<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceipeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipe', function (Blueprint $table) {
            $table->id();
            $table->string('receipe_name')->nullable();
            $table->string('cooking_time')->nullable();
            $table->string('receipe_type')->nullable();
            $table->string('directions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('receipe');
    }
}
