<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->engine = 'innodb';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigIncrements('id')->comment('主键 id');
            $table->unsignedBigInteger('user_id')->default(0)->comment('关联用户id');
            $table->string('role',255)->default('admin')->comment('管理员角色:user,admin');

            $table->string('name',255)->default('')->comment('管理员名称');
            $table->string('avatar',255)->default('')->comment('管理员头像');
            $table->string('signature')->default('')->comment('签名');
            $table->unsignedTinyInteger('gender')->default(0)->comment('性别:0-未知,1-男,2-女');
            $table->char('birthday', 10)->default('')->comment('生日');

            $table->string('mobile',30)->default('')->comment('手机');
            $table->char('mail', 255)->default('')->comment('邮箱');
            $table->char('qq', 255)->default('')->comment('QQ号');
            $table->string('password',255)->default('')->comment('密码');

            $table->text('client_driver')->comment('客户端信息:浏览器信息');
            $table->unsignedTinyInteger('client_type')->default(0)->comment('客户端类型:0未知,1-WEB,2-WEP,3-APP');
            $table->unsignedDecimal('lat',10,6)->default(0.0)->comment('坐标:纬度');
            $table->unsignedDecimal('lng',10,6)->default(0.0)->comment('坐标:经度');
            $table->string('province')->default("")->comment('省');
            $table->string('city')->default("")->comment('市');

            $table->unsignedTinyInteger('reg_method')->default(0)->comment('注册方式:0-无,1-ID+密码,2-手机+密码,3-手机+验证码,4-微信+Openid,5-QQ+Openid ');
            $table->char('reg_ip', 15)->default('')->comment('注册IP');
            $table->unsignedTinyInteger('login_method')->default(0)->comment('最后登录方式:0-无,1-ID+密码,2-手机+密码,3-手机+验证码,4-微信+Openid,5-QQ+Openid ');
            $table->char('login_ip', 15)->default('')->comment('最后登录IP');

            $table->unsignedTinyInteger('type')->default(2)->comment('用户类型:1-访客,2-用户');
            $table->unsignedTinyInteger('status')->default(2)->comment('启用状态:1-未启用,2-启用中,3-禁用');

            $table->dateTime('on_line_time')->nullable()->comment('登录时间');
            $table->dateTime('off_line_time')->nullable()->comment('登出时间');

            $table->index('id');
            $table->index('user_id');
            $table->index('role');
            $table->index(['id','role']);
            $table->index(['lat','lng']);
            $table->index('client_type');
            $table->index('reg_method');
            $table->index('login_method');
            $table->index('status');

            $table->dateTime('created_at')->comment('创建时间|注册时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');
        });

        //补充表注释
        DB::statement(" ALTER TABLE admins COMMENT '管理员表' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
