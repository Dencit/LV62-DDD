<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOauthRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_roles', function (Blueprint $table) {
            $table->engine    = 'innodb';
            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';

            $table->bigIncrements('id')->comment("管理员角色 表id");

            $table->string('scope_id', 255)->default('')->comment('授权范围-标记:字符串');

            $table->string("role")->comment("角色标记:英文单词 程序调用");
            $table->string("role_title")->comment("角色名称");
            $table->string("role_info")->comment("角色描述");

            $table->string("role_auths")->comment("角色权限表id集:json {'all','story','video','...'}");

            $table->unsignedTinyInteger("type")->default(1)->comment("角色类型:1-前台角色,2-后台角色");
            $table->unsignedTinyInteger("status")->default(1)->comment("启用状态:1-未启用,2-已启用");

            $table->index("id");
            $table->index("type");
            $table->index("status");

            $table->dateTime('created_at')->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->comment('删除时间');
        });

        //补充表注释
        DB::statement(" ALTER TABLE oauth_roles COMMENT '授权角色表' ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth_roles');
    }
}
