<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSigneeStatusFieldBookingMatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_matches', function (Blueprint $table) {
            $table->enum('signee_status', ['Matching','Interested'])->default('Matching')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_matches', function (Blueprint $table) {
            //
        });
    }
}
