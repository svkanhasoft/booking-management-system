<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking_matches', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('organization_id');
            $table->unsignedInteger('signee_id');
            $table->unsignedInteger('booking_id');
            $table->unsignedInteger('trust_id');
            $table->integer('match_count')->default(0);
            $table->date('booking_date');
            $table->enum('booking_status', ['OPEN', 'UNCONFIRMED', 'CONFIRMED'])->default('OPEN');
            $table->unsignedInteger('shift_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('signee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trust_id')->references('id')->on('trusts')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('organization_shift')->onDelete('cascade');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('booking_matches');
    }
}
