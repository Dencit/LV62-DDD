<?php

namespace Modules\Admin\Http\Trans;

use Modules\Base\Tran\BaseTran;

/**
 * notes: 数据单元 输出转化器
 */
class AdminTrans extends BaseTran
{
    //Model输出对象-转化器
    public function transform($item)
    {
        //对每一行数据字段做输出转换和过滤

//v关联模型区域

        /*
        if (isset($item['user']) && in_array('user', $this->includeArr)) {
            //调用副表同名转化器 - 转换关联副表字段
            $packData = $this->_include($item, 'user', UserTransformer::class, 'transform');
            if ($packData) {
                $item['user'] = $packData;
            }
        }
        */

//^关联模型区域

        return $item;
    }

}