<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentStatusBookingMatch extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_matches', function (Blueprint $table) {
            $table->enum('payment_status', ['PAID','UNPAID', 'ONHOLD'])->default('UNPAID');
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
