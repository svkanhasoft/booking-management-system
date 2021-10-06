<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityStatusSigneeOrganization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signee_organization', function (Blueprint $table) {
            $table->enum('activity_status', ["ACTIVE", "INACTIVEORDORMANT"])->default('ACTIVE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signee_organization', function (Blueprint $table) {
            //
        });
    }
}
