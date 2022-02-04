<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileStatusInOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signee_organization', function (Blueprint $table) {
            $table->enum('profile_status', ['Active', 'Inactive', 'Dormant'])->default('Active')->after('status');
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
