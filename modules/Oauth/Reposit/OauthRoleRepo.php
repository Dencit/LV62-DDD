<?php

namespace Modules\Oauth\Reposit;

use Modules\Oauth\Errors\OauthRootError;
use Extend\Util\QueryMatch;
use Modules\Base\Exception\Exception;
use Modules\Base\Repository\BaseRepository;
use Modules\Oauth\Models\OauthRoleModel;

/**
 * notes: 领域层-仓储类
 * 说明: 只写数据操作,不写别的内容,对应同名model
 * 调用原则: 向下调用[模型类]
 */
class OauthRoleRepo extends BaseRepository
{


    //初始化 self::$query 模型对象
    protected $model = OauthRoleModel::class;

    /*
     * 获取query查询表达式参数 - 适合不依赖 Model::scope 的场景
     * 通常不需要修改,可在同一个数据单元内共用. 如果需要附加查询条件,可调用 scopeExtend().
     */
    public function queryMatchCollect(QueryMatch $QM)
    {
        $query = self::$query;

        //?_search='action_name', 根据search动作设置其它query的运算符, 如 &param=1 中 "=" 的运算符含义是">=".
        $rule = null;
        $QM->searchAction($action);
        if ($action == 'default') {
            //$rule = ['id' => '>='];
        }

        //捕捉 ?param=1 &... 的值(包含动作设置运算符), 转化成查询数组
        $filterArr = (new OauthRoleModel())->getFieldKeys();
        $QM->search($searchArr, $rule, $filterArr);
        $QM->whereClosure(
            $searchArr, function ($data) use (&$query) {
            if ($data[1] == 'in') {
                $query->whereIn($data[0], explode(",", $data[2]));
            } else {
                $query->where($data[0], $data[1], $data[2]);
            }
        }
        );

        //?_include=user,info
        $QM->include($includeArr);
        $QM->incModelHaveClosure(
            OauthRoleModel::class, $includeArr, function ($include) use (&$query) {
            $query->with($include);
        }
        );

        //?_where_in_sort=status/1,2,3 //按id顺序返回结果
        $QM->whereInSort($whereInSortArr, $sortItem);
        $QM->whereInSortClosure(
            $whereInSortArr, $sortItem, function ($data, $rawStr) use (&$query) {
            $query->whereIn($data[0], explode(",", $data[2]));
            $query->orderByRaw($rawStr);
        }
        );

        //?_sort = -id
        $QM->sort($sortArr);
        $QM->sortClosure(
            $sortArr, function ($key, $val) use (&$query) {
            $query->orderBy($key, $val);
        }
        );

    }

    /*
     * 副表查询扩展 - 扩展类型+传参方式 - 用于附加查询条件,不是数据输出.
     * 对query: 'extend' 参数的获取, 作为关联查询的触发条件, 编写具体逻辑作用到当前查询中
     * where子查询: https://www.kancloud.cn/manual/thinkphp6_0/1037569
     */
    public function scopeExtend(array $requestQuery)
    {
        $_extend = $requestQuery['_extend'] ?? null;
        $extendArr = explode(',', $_extend);
        if (in_array('param', $extendArr)) {
            $query = self::$query;
            //副表条件 - 子查询附加条件到主表, 降低直接联表的时间复杂度(笛卡尔积).
            //$query->whereIn('id', function ($childQuery) use ($requestQuery) {});
        }
    }

    /*
     * 获取query查询表达式参数 - 适合不依赖 Model::scope 的场景
     * 通常不需要修改,可在同一个数据单元内共用. 如果需要附加查询条件,可调用 scopeExtend().
     */
    public function queryMatchDetail(QueryMatch $QM)
    {
        $query = self::$query;

        //?_search='action_name', 根据search动作设置其它query的运算符, 如 &param=1 中 "=" 的运算符含义是">=".
        $rule = null;
        $QM->searchAction($action);
        if ($action == 'default') {
            //$rule = ['id' => '>='];
        }

        //捕捉 ?param=1 &... 的值(包含动作设置运算符), 转化成查询数组
        $filterArr = (new OauthRoleModel())->getFieldKeys();
        $QM->search($searchArr, $rule, $filterArr);
        $QM->whereClosure(
            $searchArr, function ($data) use (&$query) {
            if ($data[1] == 'in') {
                $query->whereIn($data[0], explode(",", $data[2]));
            } else {
                $query->where($data[0], $data[1], $data[2]);
            }
        }
        );

        //?_include=user,info
        $QM->include($includeArr);
        $QM->incModelHaveClosure(
            OauthRoleModel::class, $includeArr, function ($include) use (&$query) {
            $query->with($include);
        }
        );

        //?_sort = -id
        $QM->sort($sortArr);
        $QM->sortClosure(
            $sortArr, function ($key, $val) use (&$query) {
            $query->orderBy($key, $val);
        }
        );

    }

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
            Exception::app(OauthRootError::code("ID_NOT_FOUND"), OauthRootError::msg("ID_NOT_FOUND"), __METHOD__);
        };
        return $result;
    }

    //检查角色 是否存在
    public static function isRoleIdExit($roleId)
    {
        $where  = ["role" => $roleId];
        $field  = ['id', 'role'];
        $field  = array_merge($field, array_keys($where));
        $result = self::$query->select($field)->where($where)->first();
        if (!$result) {
            Exception::app(OauthRootError::code("ROLE_ID_NOT_FOUND"), OauthRootError::msg("ROLE_ID_NOT_FOUND"), __METHOD__);
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
            Exception::app(OauthRootError::code("ID_NOT_UNIQUE"), OauthRootError::msg("ID_NOT_UNIQUE"), __METHOD__);
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
            Exception::app(OauthRootError::code("BATCH_IDS_NOT_FOUND"), OauthRootError::msg("BATCH_IDS_NOT_FOUND"), __METHOD__);
        };
        return $result;
    }

}