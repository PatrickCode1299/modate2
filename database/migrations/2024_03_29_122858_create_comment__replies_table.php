<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment__replies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text("comment_reply");
            $table->text("comment_file");
            $table->text("initial_comment_id");
            $table->text("reply_id");
            $table->text("user_being_replied");
            $table->text("user_who_replied");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment__replies');
    }
}
