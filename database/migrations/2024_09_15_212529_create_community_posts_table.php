<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text("name")->nullable();
            $table->text("avatar")->nullable();
            $table->text("caption")->nullable();
            $table->text("email")->nullable();
            $table->text("post_img1")->nullable();
            $table->text("post_img2")->nullable();
            $table->text("post_img3")->nullable();
            $table->text("post_img4")->nullable();
            $table->text("video")->nullable();
            $table->text('postid')->nullable();
            $table->text('isReply')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('community_posts');
    }
}
