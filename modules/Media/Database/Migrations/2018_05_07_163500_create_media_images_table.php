<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateMediaImagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_images', function(Blueprint $table)
        {
            $table->engine = 'innodb';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->bigIncrements('id')->comment('图片媒介表id');
            $table->string('host')->comment('根域名');
            $table->string('uri')->comment('相对路径');
            $table->unsignedTinyInteger("type")->default(0)->comment('图片用途:1-头像,2-封面');
            $table->unsignedTinyInteger('status')->default(2)->comment('启用状态:1-未启用,2-启用中,3-禁用');

            $table->dateTime('created_at')->comment('创建时间|注册时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');

            $table->index('id');
            $table->index('type');
        });

        //补充表注释
        DB::statement(" ALTER TABLE media_images COMMENT '图片媒介表' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('media_images');
    }

}
