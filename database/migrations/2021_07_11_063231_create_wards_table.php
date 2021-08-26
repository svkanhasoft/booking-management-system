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
            $table->unsignedInteger('ward_type_id')->nullable();
            // $table->string('ward_type');
            $table->string('ward_type');
            $table->integer('ward_number');
            $table->softDeletes();
            $table->timestamps();

            

            $table->foreign('ward_type_id')->references('id')->on('ward_type')->onDelete('cascade');
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
