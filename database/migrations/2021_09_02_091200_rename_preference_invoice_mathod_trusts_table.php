<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePreferenceInvoiceMathodTrustsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trusts', function (Blueprint $table) {
            $table->renameColumn('preference_invoive_method', 'preference_invoice_method');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trusts', function (Blueprint $table) {
            //
        });
    }
}
