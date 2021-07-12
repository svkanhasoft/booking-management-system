<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTrustsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trusts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('name');
            $table->string('code');
            // $table->string('preference_invoive_method');
            $table->enum('preference_invoive_method', ['BYEmail', 'BYPost'])->default('BYEmail');
            $table->string('email_address');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('city');
            $table->string('post_code');
            $table->string('trust_portal_url');
            $table->string('portal_email');
            $table->string('portal_password');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('contact_email_address');
            $table->integer('phone_number');
            $table->string('client')->nullable();
            $table->string('department')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('trusts');
    }
}
