<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharedPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shared__posts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('name')->nullable();
            $table->text('avatar')->nullable();
            $table->text('caption')->nullable();
            $table->text('email')->nullable();
            $table->text("post_img1")->nullable();
            $table->text("post_img2")->nullable();
            $table->text("post_img3")->nullable();
            $table->text("post_img4")->nullable();
            $table->text("video")->nullable();
            $table->text("quote")->nullable();
            $table->text('postid')->nullable();
            $table->text('prev_id')->nullable();
            $table->text("name_of_user_who_shared")->nullable();
            $table->text("email_of_user_who_shared")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shared__posts');
    }
}
