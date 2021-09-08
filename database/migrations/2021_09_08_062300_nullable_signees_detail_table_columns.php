<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NullableSigneesDetailTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signees_detail', function (Blueprint $table) {
            $table->string('nationality', 191)->nullable()->change();
            $table->string('candidate_referred_from', 191)->nullable()->change();
            $table->string('date_of_birth')->nullable()->change();
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
