<?php

namespace Modules\Oauth\Database\Seeders;

use Modules\Base\Database\BaseSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class OauthScopeTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        //DB::table("oauth_scopes")->truncate();//清空

        $data = [
            ['scope' => '用户端授权-范围', 'scope_id' => 'user_auth', 'created_at' => date('Y-m-d H:i:s') ],
            ['scope' => '管理端授权-范围', 'scope_id' => 'admin_auth', 'created_at' => date('Y-m-d H:i:s') ],
            ['scope' => '系统端授权-范围', 'scope_id' => 'system_auth', 'created_at' => date('Y-m-d H:i:s') ],
        ];

        $scopeIds = array_column($data,'scope_id');
        //获取旧数据 - 去重
        $rows = $this->oldDataExit('oauth_scopes','scope_id',$scopeIds);
        foreach ($data as $ind=>$column ){
            $value = $column['scope_id'];
            if( in_array($value,$rows) ){ unset($data[$ind]); }
        }

        if(!empty($data)){
            $data = array_values($data);
            DB::table('oauth_scopes')->insert($data);
        }

    }
}
