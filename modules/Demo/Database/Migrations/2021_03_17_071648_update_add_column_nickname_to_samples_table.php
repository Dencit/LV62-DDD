<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateAddColumnNicknameToSamplesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("samples", function (Blueprint $table) {
            $table->char('nickname')->default('')->comment('用户昵称')->after("name");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("samples", function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }

}
