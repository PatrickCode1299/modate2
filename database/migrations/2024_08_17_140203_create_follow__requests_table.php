<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follow__requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('isPending')->nullable();
            $table->text('user_who_is_followed')->nullable();
            $table->text('user_who_wants_to_follow')->nullable();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follow__requests');
    }
}
