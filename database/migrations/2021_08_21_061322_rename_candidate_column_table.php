<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCandidateColumnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_matches', function (Blueprint $table) {
            // $table->dropForeign('booking_matches_candidate_id_foreign');
            // $table->renameColumn('candidate_id', 'signee_id');
            // $table->foreign('signee_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_matches', function (Blueprint $table) {
            //
        });
    }
}
