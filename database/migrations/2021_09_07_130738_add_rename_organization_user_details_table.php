<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRenameOrganizationUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('organization_user_details', function (Blueprint $table) {
        //     Schema::rename('organization_user_details', 'organization_staff_details');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organization_user_details', function (Blueprint $table) {
            //
        });
    }
}
