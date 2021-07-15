<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNmcDmcPinUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signees_detail', function (Blueprint $table) {
            $table->string('cv')->after('date_registered');
            $table->string('nmc_dmc_pin')->after('date_registered')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signees_detail', function (Blueprint $table) {
            //
        });
    }
}
