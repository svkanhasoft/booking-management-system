<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSigneePreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signee_preference', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');

	        $table->boolean('monday_day')->default(0);
            $table->boolean('monday_night')->default(0);

            $table->boolean('tuesday_day')->default(0);
            $table->boolean('tuesday_night')->default(0);

            $table->boolean('wednesday_day')->default(0);
            $table->boolean('wednesday_night')->default(0);

            $table->boolean('thursday_day')->default(0);
            $table->boolean('thursday_night')->default(0);

            $table->boolean('friday_day')->default(0);
            $table->boolean('friday_night')->default(0);

            $table->boolean('saturday_day')->default(0);
            $table->boolean('saturday_night')->default(0);

            $table->boolean('sunday_day')->default(0);
            $table->boolean('sunday_night')->default(0);

            $table->string('no_of_shift');
            $table->boolean('is_travel')->default(0);
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        //
    }
}
