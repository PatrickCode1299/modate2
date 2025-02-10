<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReplyToSharedPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shared__posts', function (Blueprint $table) {
            $table->text('isReply')->after('prev_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shared__posts', function (Blueprint $table) {
            $table->text('isReply')->after('prev_id');
        });
    }
}
