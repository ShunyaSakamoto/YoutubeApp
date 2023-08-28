<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id('object_id');
            $table->string('video_id');
            $table->string('title');
            $table->string('category_id');
            $table->string('channel_id');
            $table->text('description');
            $table->string('duration');
            $table->string('caption');
            $table->dateTime('published_at')->default('2999-12-31');
            $table->string('upload_status');
            $table->string('privacy_status');
            $table->string('video_url');
            $table->string('thumbnail_url');
            $table->bigInteger('view_count')->nullable()->comment('null: 非公開');
            $table->bigInteger('like_count')->nullable()->comment('null: 非公開');
            $table->bigInteger('dislike_count')->nullable()->comment('常にnull');
            $table->bigInteger('favorite_count')->default(0)->comment('常に0');
            $table->bigInteger('comment_count')->nullable('null: 非公開');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
};
