<?php

namespace Modules\Oauth\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

/**
 * notes: seeder 数据填充 主控类
 * @author 陈鸿扬 | @date 2021/3/16 17:27
 * Class OauthDatabaseSeeder
 * @package Modules\Oauth\Database\Seeders
 */
class OauthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(OauthClientTableSeeder::class);
        $this->call(OauthScopeTableSeeder::class);
        $this->call(OauthRoleTableSeeder::class);
    }
}
