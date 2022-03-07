<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('organization_id');
            $table->string('subscription_name');
            $table->date('subscription_purchase_date');
            $table->date('subscription_expire_date');
            $table->decimal('subscription_price')->default("0");
            $table->enum('payment_status', ['Success', 'Failed', 'Hold','Inprogress']);
            $table->json('paypal_response')->nullable();
            // $table->longText('paypal_response')->nullable();
            $table->foreign('organization_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('payment_log');
    }
}
