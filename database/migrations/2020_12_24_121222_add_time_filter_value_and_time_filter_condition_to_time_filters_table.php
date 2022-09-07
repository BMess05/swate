<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimeFilterValueAndTimeFilterConditionToTimeFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('time_filters', function (Blueprint $table) {
            $table->string('time_filter_value')->nullable()->after('time_filter_name');
            $table->string('time_filter_condition')->nullable()->after('time_filter_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('time_filters', function (Blueprint $table) {
            //
        });
    }
}
