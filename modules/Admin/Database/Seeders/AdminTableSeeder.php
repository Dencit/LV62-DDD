<?php

namespace Modules\Admin\Database\Seeders;

use Modules\Base\Database\BaseSeeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminTableSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        //DB::table("admins")->truncate();//清空

        $data = [
            [
                'id'            => 1,
                'user_id'       => 0,
                'role'          => 'system',
                'name'          => '超级管理员',
                'mobile'        => '18588891945',
                'password'      => 'C01B0oArq8aMhlMmSdRbDA==',
                'client_driver' => 'none',
                'status'        => 2,
                'created_at'    => date('Y-m-d H:i:s')
            ],
        ];

        $ids = array_column($data, 'id');
        //获取旧数据 - 去重
        $rows = $this->oldDataExit('admins', 'id', $ids);
        foreach ($data as $ind => $column) {
            $value = $column['id'];
            if (in_array($value, $rows)) {
                unset($data[$ind]);
            }
        }

        if (!empty($data)) {
            $data = array_values($data);
            DB::table('admins')->insert($data);
        }

    }
}
