<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToChannelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void {
        Schema::table('channels', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id'); // Add column
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Add FK
        });
    }

    public function down(): void {
        Schema::table('channels', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
