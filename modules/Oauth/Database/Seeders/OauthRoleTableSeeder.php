<?php

namespace Modules\Oauth\Database\Seeders;

use Modules\Base\Database\BaseSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OauthRoleTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        //DB::table("oauth_roles")->truncate();//清空

        $createdAt = date("Y-m-d H:i:s", time());
        $updatedAt = date("Y-m-d H:i:s", time());
        $data      = [
            ['scope_id' => 'user_auth',
             "role"     => "user", "role_title" => "普通用户", "role_info" => "普通用户说明", "role_auths" => "[\"user\"]",
             "type"     => 1, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id' => 'admin_auth',
             "role"     => "editor", "role_title" => "编辑", "role_info" => "编辑说明", "role_auths" => "[\"edit\"]",
             "type"     => 2, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id' => 'admin_auth',
             "role"     => "accountant", "role_title" => "财务", "role_info" => "财务说明", "role_auths" => "[\"admin\",\"accountant\"]",
             "type"     => 2, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id' => 'admin_auth',
             "role"     => "admin", "role_title" => "普通管理员", "role_info" => "普通管理员说明", "role_auths" => "[\"admin\",\"edit\"]",
             "type"     => 2, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id' => 'system_auth',
             "role"     => "system", "role_title" => "系统管理员", "role_info" => "系统管理员说明", "role_auths" => "[\"super\",\"admin\",\"edit\",\"accountant\"]",
             "type"     => 2, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt],
            ['scope_id' => 'admin_auth',
             "role"     => "robot", "role_title" => "机器人", "role_info" => "机器人说明", "role_auths" => "[\"edit\"]",
             "type"     => 2, "status" => 2, 'created_at' => $createdAt, 'updated_at' => $updatedAt]
        ];

        $ids = array_column($data, 'role');
        //获取旧数据 - 去重
        $rows = $this->oldDataExit('oauth_roles', 'role', $ids);
        foreach ($data as $ind => $column) {
            $value = $column['role'];
            if (in_array($value, $rows)) {
                unset($data[$ind]);
            }
        }

        if (!empty($data)) {
            $data = array_values($data);
            DB::table('oauth_roles')->insert($data);
        }

    }
}
