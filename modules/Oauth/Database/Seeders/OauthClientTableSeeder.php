<?php

namespace Modules\Oauth\Database\Seeders;

use Modules\Base\Database\BaseSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OauthClientTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        //DB::table("oauth_clients")->truncate();//清空

        $createdAt = date("Y-m-d H:i:s", time());
        $updatedAt = date("Y-m-d H:i:s", time());
        $data      = [
            ['scope_id'     => 'user_auth',
             'client_title' => 'H5端', 'client_info' => 'H5端说明', 'client_id' => 'h5_client', 'client_secret' => $this->createSecret(64),
             "type"         => 1, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id'     => 'user_auth',
             'client_title' => '微信端', 'client_info' => '微信端说明', 'client_id' => 'wechat_client', 'client_secret' => $this->createSecret(64),
             "type"         => 1, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id'     => 'user_auth',
             'client_title' => '安卓端', 'client_info' => '安卓端说明', 'client_id' => 'android_client', 'client_secret' => $this->createSecret(64),
             "type"         => 1, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id'     => 'user_auth',
             'client_title' => 'IOS端', 'client_info' => 'IOS端说明', 'client_id' => 'ios_client', 'client_secret' => $this->createSecret(64),
             "type"         => 1, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id'     => 'admin_auth',
             'client_title' => '后台管理端', 'client_info' => '后台管理端说明', 'client_id' => 'admin_client', 'client_secret' => $this->createSecret(64),
             "type"         => 2, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id'     => 'system_auth',
             'client_title' => '后台系统管理端', 'client_info' => '后台系统管理端说明', 'client_id' => 'system_client', 'client_secret' => $this->createSecret(64),
             "type"         => 2, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
        ];


        $ids = array_column($data, 'client_id');
        //获取旧数据 - 去重
        $rows = $this->oldDataExit('oauth_clients', 'client_id', $ids);
        foreach ($data as $ind => $column) {
            $value = $column['client_id'];
            if (in_array($value, $rows)) {
                unset($data[$ind]);
            }
        }

        if (!empty($data)) {
            $data = array_values($data);
            DB::table('oauth_clients')->insert($data);
        }

    }


}
