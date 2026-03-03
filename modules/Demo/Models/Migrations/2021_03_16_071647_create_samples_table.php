<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->engine = 'innodb';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id')->comment('主键 id');

            $table->string('name',50)->default('')->comment('用户昵称');
            $table->string('mobile',30)->default('')->comment('绑定手机');
            $table->string('photo',200)->default('')->comment('用户头像');

            $table->unsignedTinyInteger('gender')->default(0)->comment('性别: 0未知, 1男, 2女');
            $table->unsignedTinyInteger('type')->default(0)->comment('类型: 0未知, 1-否, 2-是');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态: 1-否, 2-是');

            $table->index('id');
            $table->index('type');
            $table->index('status');

            $table->dateTime('created_at')->comment('创建时间|注册时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');
        });

        //补充表注释
        DB::statement(" ALTER TABLE samples COMMENT '模板表' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('samples');
    }
}
