<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRateFielsInTrustsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trusts', function (Blueprint $table) {
            $table->integer('payable_holiday_rate')->after('department')->default(0);
            $table->integer('payable_saturday_rate')->after('department')->default(0);
            $table->integer('payable_night_rate')->after('department')->default(0);
            $table->integer('payable_day_rate')->after('department')->default(0);

            $table->integer('chargeable_holiday_rate')->after('department')->default(0);
            $table->integer('chargeable_saturday_rate')->after('department')->default(0);
            $table->integer('chargeable_night_rate')->after('department')->default(0);
            $table->integer('chargeable_day_rate')->after('department')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trusts', function (Blueprint $table) {
            //
        });
    }
}
