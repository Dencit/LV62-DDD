<?php

namespace Modules\Demo\Srv;

use Extend\Util\QueryMatch;
use Modules\Base\Exception\Exception;
use Modules\Base\Srv\BaseSrv;
use Modules\Demo\Enums\EsSampleEnum;
use Modules\Demo\EDocs\EsSampleEDoc;
use Modules\Demo\Errors\DemoRootError;

/**
 * notes: 数据单元服务
 * @author 陈鸿扬 | @date 2021/1/20 21:49
 */
class EsSampleSrv extends BaseSrv
{
    /*
     * -新增-索引库
     */
    public function esSampleTableSave(&$requestInput)
    {
        $EDoc = EsSampleEDoc::instance(EsSampleEnum::VERSION);
        $EDoc->schema(EsSampleEnum::VERSION);
        return true;
    }

    /*
     * 新增数据 -
     */
    public function esSampleSave(&$requestInput)
    {
        $EDoc = EsSampleEDoc::instance(EsSampleEnum::VERSION);
        $EDoc->table(EsSampleEnum::TABLE);

        $result = $EDoc->saveById($requestInput['id'], $requestInput);

        return $result;
    }

    /*
     * 批量新增数据 - 
     */
    public function esSampleBatchSave(&$requestInput)
    {
        $EDoc = EsSampleEDoc::instance(EsSampleEnum::VERSION);
        $EDoc->table(EsSampleEnum::TABLE);

        $result = $EDoc->saveAll($requestInput);

        return $result;
    }

    /*
     * 根据 主键id 更新详情 -
     */
    public function esSampleUpdate($id, &$requestInput)
    {
        $EDoc = EsSampleEDoc::instance(EsSampleEnum::VERSION);
        $EDoc->table(EsSampleEnum::TABLE);

        $isExit = $EDoc->find($id);
        if ($isExit->isEmpty()) {
            Exception::app(DemoRootError::code("ID_NOT_FOUND"), DemoRootError::msg("ID_NOT_FOUND"), __METHOD__);
        }
        $result = $EDoc->update($id, $requestInput);

        return $result;
    }

    /*
     * 根据 主键id 批量更新 - 
     */
    public function esSampleBatchUpdate(&$requestInput)
    {
        //业务逻辑
        $ids = array_column($requestInput, 'id');

        $EDoc = EsSampleEDoc::instance(EsSampleEnum::VERSION);
        $EDoc->table(EsSampleEnum::TABLE);

        $result = $EDoc->updateAll($requestInput);

        return $result;
    }

    /*
     * 列表筛选 - 
     */
    public function esSampleIndex(array $requestQuery)
    {

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);
        //
        $EDoc = EsSampleEDoc::instance(EsSampleEnum::VERSION);
        $EDoc->table(EsSampleEnum::TABLE);

        //?_search='action_name', 根据search动作设置其它query的运算符, 如 &param=1 中 "=" 的运算符含义是">=".
        $rule = null;
        $QM->searchAction($action);
        if ($action == 'default') {
            //$rule = ['id' => '>='];
        }

        //捕捉 ?param=1 &... 的值(包含动作设置运算符), 转化成查询数组
        $filterArr = $EDoc->getFieldKeys();
        $QM->search($searchArr, $rule, $filterArr);
        $QM->whereClosure(
            $searchArr, function ($data) use (&$EDoc) {
            if ($data[1] == 'in') {
                $EDoc->whereIn($data[0], explode(",", $data[2]));
            } else {
                $EDoc->where($data[0], $data[1], $data[2]);
            }
        }
        );

        //?_sort = -id
        $QM->sort($sortArr);
        $QM->sortClosure(
            $sortArr, function ($key, $val) use (&$EDoc) {
            $EDoc->order($key, $val);
        }
        );

        //默认排序
        $EDoc->order('doc_update_time', 'desc');

        $QM->group($groupArr);
        $EDoc->groupBy($groupArr);

        $QM->pagination($perpage, $page);
        $EDoc->page($page, $perpage);

        $EDoc->select();
        //dd( $EDoc->toDSL() );//
        //dd( $EDoc->toSource() );//
        $result = $EDoc->toArray();
        //dd($result);//

        return $result;
    }

    /*
     * 根据 主键id 获取详情 - 
     */
    public function esSampleRead(array $requestQuery, $id)
    {
        //业务逻辑
        //{@field_detail
        $fields = ['*'];
        //@field_detail}

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);
        //
        $EDoc = EsSampleEDoc::instance(EsSampleEnum::VERSION);
        $EDoc->table(EsSampleEnum::TABLE)->fields($fields);

        //?_search='action_name', 根据search动作设置其它query的运算符, 如 &param=1 中 "=" 的运算符含义是">=".
        $rule = null;
        $QM->searchAction($action);
        if ($action == 'default') {
            //$rule = ['id' => '>='];
        }

        //捕捉 ?param=1 &... 的值(包含动作设置运算符), 转化成查询数组
        $filterArr = $EDoc->getFieldKeys();
        $QM->search($searchArr, $rule, $filterArr);
        $QM->whereClosure(
            $searchArr, function ($data) use (&$EDoc) {
            if ($data[1] == 'in') {
                $EDoc->whereIn($data[0], explode(",", $data[2]));
            } else {
                $EDoc->where($data[0], $data[1], $data[2]);
            }
        }
        );

        //?_sort = -id
        $QM->sort($sortArr);
        $QM->sortClosure(
            $sortArr, function ($key, $val) use (&$EDoc) {
            $EDoc->order($key, $val);
        }
        );

        //默认排序
        $EDoc->order('doc_update_time', 'desc');

        if (!empty($id)) {
            $EDoc->find($id);
        } else {
            $EDoc->first();
        }

        //dd( $EDoc->toDSL() );//
        //dd( $EDoc->toSource() );//
        $result = $EDoc->toArray();
        //dd($result);//

        return $result;
    }

    /*
     * 根据 主键id 删除详情 - 
     */
    public function esSampleDelete($id)
    {
        $EDoc = EsSampleEDoc::instance(EsSampleEnum::VERSION);
        $EDoc->table(EsSampleEnum::TABLE);

        $isExit = $EDoc->find($id);
        $result = $isExit->toArray();

        if ($isExit->isEmpty()) {
            Exception::app(DemoRootError::code("ID_NOT_FOUND"), DemoRootError::msg("ID_NOT_FOUND"), __METHOD__);
        }

        $EDoc->delete();

        return $result;
    }

}
