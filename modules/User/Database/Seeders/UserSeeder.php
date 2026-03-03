<?php namespace Modules\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $createdAt = date("Y-m-d H:i:s", time());
        $updatedAt = date("Y-m-d H:i:s", time());
        $data      = [
            [
                "id"              => 100000,
                "nickname"        => "初始用户-昵称",
                "name"            => "初始用户-姓名",
                "signature"       => "初始用户-签名",
                "avatar"          => "",
                "mobile"          => "18588891945",
                "gender"          => 1,
                "password"        => 'C01B0oArq8aMhlMmSdRbDA==',
                "reg_method" => 1,
                'created_at'      => $createdAt,
                'updated_at'      => $updatedAt,
            ],
        ];

        //检查旧数据重复
        $inIds  = [100000];
        $count  = DB::table('users')->count();
        $result = DB::table('users')->select("id")->whereIn("id", $inIds)->get();

        if (!empty($result)) {
            $result = $result->toArray();
            $result = array_column($result, "id");
            foreach ($data as $k => $v) {
                if (in_array($v["id"], $result)) {
                    unset($data[$k]);
                }
            }
        }
        if ($count < 1) {
            //卸载表 重新加默认数据
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table("users")->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        //
        DB::table('users')->insert($data);

    }

}