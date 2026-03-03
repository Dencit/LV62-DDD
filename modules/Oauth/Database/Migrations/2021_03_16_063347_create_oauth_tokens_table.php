<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOauthTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_tokens', function (Blueprint $table) {
            $table->engine = 'innodb';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id')->comment('主键 id');

            $table->string('user_mark',255)->default('')->comment('用户标记: user_1,admin_1');

            $table->string('scope_id',255)->default('')->comment('授权范围-标记: 字符串');
            $table->string('client_id',255)->default('')->comment('授权客户端-标记: 字符串');
            $table->string('client_secret',255)->default('')->comment('授权客户-密匙: 字符串');

            $table->text('token')->comment('授权信息: 字符串');
            $table->text('refresh_token')->nullable()->comment('刷新授权信息: 字符串');

            $table->dateTime('start_time')->comment('开始时间');
            $table->dateTime('expire_time')->comment('过期时间');

            $table->index('id');
            $table->index('scope_id');
            $table->index('client_id');
            $table->index(['scope_id','client_id']);
            $table->index('user_mark');

            $table->dateTime('created_at')->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');
        });

        //补充表注释
        DB::statement(" ALTER TABLE oauth_tokens COMMENT '授权信息表' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_tokens');
    }
}
