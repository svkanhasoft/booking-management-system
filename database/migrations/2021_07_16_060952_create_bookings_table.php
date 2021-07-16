<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('reference_id');
            $table->unsignedInteger('trust_id');
            $table->unsignedInteger('ward_id');
            $table->unsignedInteger('shift_id');
            $table->date('date');
            $table->integer('grade_id');
            $table->enum('status', ['OPEN', 'UNCONFIRMED','CONFIRMED'])->default('OPEN');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ward_id')->references('id')->on('ward')->onDelete('cascade');
            $table->foreign('trust_id')->references('id')->on('trusts')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('organization_shift')->onDelete('cascade');
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bookings');
    }
}
