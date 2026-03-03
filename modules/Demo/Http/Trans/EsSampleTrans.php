<?php
namespace Modules\Demo\Http\Trans;

use Modules\Base\Tran\BaseTran;

/**
 * notes: 数据单元 输出转化器
 * @author 陈鸿扬 | @date 2021/2/3 10:00
 */
class EsSampleTrans extends BaseTran
{
    //Model输出对象-转化器
    public function transform($item)
    {
        //对每一行数据字段做输出转换和过滤


        return $item;
    }

}