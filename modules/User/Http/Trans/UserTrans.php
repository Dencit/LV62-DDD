<?php

namespace Modules\User\Http\Trans;

use Modules\Base\Tran\BaseTran;

/**
 * notes: 应用层-输出转化类
 * 说明: 当输出前需要对数据做遍历处理时, 在 ApiTrans 执行时 调用此转化器逻辑, 对每一列数据 做统一处理.
 */
class UserTrans extends BaseTran
{
    //Model输出对象-转化器
    public function transform($item)
    {
        //对每一行数据字段做输出转换和过滤

        if (isset($item['password'])) {
            unset($item['password']);
        }

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