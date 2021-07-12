<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTraningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traning', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('trust_id');
            $table->string('traning_name');
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
        Schema::drop('traning');
    }
}
