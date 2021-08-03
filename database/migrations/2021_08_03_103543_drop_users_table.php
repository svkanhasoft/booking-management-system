<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('last_login_date')->nullable()->after('contact_number');
            $table->renameColumn('is_verified', 'password_change');
            $table->dropColumn(['created_by', 'updated_by','ip_address']);
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['address_line_1', 'contact_number','address_line_2','city','postcode']);
        });
        Schema::table('signees_detail', function (Blueprint $table) {
            $table->dropColumn(['address_line_1', 'address_line_2','address_line_3','city','post_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
