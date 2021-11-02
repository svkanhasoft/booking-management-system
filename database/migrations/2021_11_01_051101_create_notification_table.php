<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('signee_id')->nullable();
            $table->unsignedInteger('orgnization_id')->nullable();
            $table->string('message');
            $table->string('status');
            $table->boolean('is_read')->default(0);
            $table->boolean('is_sent')->default(0);

            $table->foreign('signee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('orgnization_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('notification');
    }
}
