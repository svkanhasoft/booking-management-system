<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ward', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trust_id');
            $table->string('ward_name');
            // $table->string('ward_type');
            $table->enum('ward_type', ['Hospital', 'GP Clinic'])->default('Hospital');
            $table->integer('ward_number');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('trust_id')->references('id')->on('trusts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ward');
    }
}
