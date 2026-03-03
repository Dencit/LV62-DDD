<?php

namespace Modules\Media\Reposit;

use Modules\Media\Errors\MediaRootError;
use Extend\Util\QueryMatch;
use Modules\Base\Exception\Exception;
use Modules\Base\Repository\BaseRepository;
use Modules\Media\Models\MediaImageModel;

/**
 * notes: 领域层-仓储类
 * 说明: 只写数据操作,不写别的内容,对应同名model
 * 调用原则: 向下调用[模型类]
 */
class MediaImageRepo extends BaseRepository
{

    //初始化 self::$query 模型对象
    protected $model = MediaImageModel::class;

//{@block_br}
//{@block_br/}

//{@block_r}
//{@block_r/}

    //根据ID获取详细
    public function isHave($id)
    {
        $where = ["id" => $id];
        $field = ['id'];
        $field = array_merge($field, array_keys($where));
        $result = self::$query->select($field)->where($where)->first();
        return $result;
    }

    //检查是否存在
    public function isExit($id)
    {
        $where = ["id" => $id];
        $field = ['id'];
        $field = array_merge($field, array_keys($where));
        $result = self::$query->select($field)->where($where)->first();
        if (!$result) {
            Exception::app(MediaRootError::code("ID_NOT_FOUND"), MediaRootError::msg("ID_NOT_FOUND"), __METHOD__);
        };
        return $result;
    }

    //检查是否重复
    public function isUnique($id)
    {
        $where = ["id" => $id];
        $field = ['id'];
        $field = array_merge($field, array_keys($where));
        $result = self::$query->select($field)->where($where)->first();
        if ($result) {
            Exception::app(MediaRootError::code("ID_NOT_UNIQUE"), MediaRootError::msg("ID_NOT_UNIQUE"), __METHOD__);
        };
        return $result;
    }

    //检查 id数量 和 返回id数量 是否相等
    public function isBatchIdsExit($ids)
    {
        $where = [];
        $field = ['id'];
        $field = array_unique(array_merge($field, array_keys($where)));
        $result = self::$query->select($field)->whereIn('id', $ids)->get();
        if (count($ids) != count($result)) {
            Exception::app(MediaRootError::code("BATCH_IDS_NOT_FOUND"), MediaRootError::msg("BATCH_IDS_NOT_FOUND"), __METHOD__);
        };
        return $result;
    }

}