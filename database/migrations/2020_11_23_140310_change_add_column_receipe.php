<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAddColumnReceipe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('receipe', function (Blueprint $table) {
            $table->string('directions', 4000)->change();
            $table->integer('category_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reciepe', function (Blueprint $table) {
            $table->dropColumn(['category_id']);
            $table->string('directions', 255)->change();
        });
    }
}
