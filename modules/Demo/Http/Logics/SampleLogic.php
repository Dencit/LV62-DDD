<?php

namespace Modules\Demo\Http\Logics;

use Modules\Base\Logic\BaseLogic;
use Modules\Demo\Jobs\SampleJob;
use Extend\Util\QueryMatch;
use Modules\Demo\Reposit\SampleRepo;

/**
 * notes: 应用层-业务类
 * 说明: 业务类数据操作,一般不直接调用模型,通过仓储类提供存粹的数据执行函数, 跨 应用端/模块 操作同一数据类型的业务, 建议抽象到 领域层-业务类, 减少冗余.
 * 调用原则: 向下调用[仓储类,领域层-业务类]
 */
class SampleLogic extends BaseLogic
{

//{@block_c}
    /*
     * 新增数据 - 模板
     */
    public function sampleSave(&$requestInput)
    {
        //业务逻辑
        $builder = SampleRepo::newInstance($requestInput);
        $builder->saveOrFail();
        $result = $builder->fresh();

        return $result;
    }
//{@block_c/}

//{@block_cj}
    /*
     * 新增队列数据 - 模板
     */
    public function sampleJobSave(&$requestInput)
    {

        //队列发送
        SampleJob::dispatch($requestInput)->onConnection('redis')->onQueue('SampleJob');

        return $requestInput;
    }
//{@block_cj/}

//{@block_bc}
    /*
     * 批量新增数据 - 模板
     */
    public function sampleBatchSave(&$requestInput)
    {
        //业务逻辑
        foreach ($requestInput as $ind => $item) {
            $itemObj = SampleRepo::updateOrCreate($item);
            $list[]  = $itemObj;
        }
        $result = $list ?? [];

        return $result;
    }
//{@block_bc/}

//{@block_u}
    /*
     * 根据 主键id 更新详情 - 模板
     */
    public function sampleUpdate($id, &$requestInput)
    {
        //业务逻辑
        $builder = SampleRepo::searchInstance();
        $builder = $builder->isExit($id);

        $builder->fill($requestInput);
        $builder->saveOrFail();
        $result = $builder->fresh();

        return $result;
    }
//{@block_u/}

//{@block_bu}
    /*
     * 根据 主键id 批量更新 - 模板
     */
    public function sampleBatchUpdate(&$requestInput)
    {
        //业务逻辑
        foreach ($requestInput as $ind => $item) {
            //业务逻辑
            $builder = SampleRepo::newInstance();
            $builder = $builder->isExit($item['id']);

            $builder->fill($item)->saveOrFail();
            $itemObj = $builder->fresh();
            $list[]  = $itemObj;
        }
        $result = $list ?? [];

        return $result;
    }
//{@block_bu/}

//{@block_br}
    /*
     * 列表筛选 - 模板
     */
    public function sampleIndex(array $requestQuery)
    {
        //业务逻辑
        //{@field_collect
        $fields = ['*'];
        //@field_collect}

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);

        $builder = SampleRepo::searchInstance($fields);
        //?key=value 范围查询
        $builder->queryMatchCollect($QM);

        //?_extend=param 副表扩展查询-用于附加查询条件,不是数据输出.
        $builder->scopeExtend($requestQuery);

        //默认排序
        $builder->orderBy('updated_at', 'desc');

        //?_pagination=true 翻页查询
        $result = $builder->pageGet($QM);
        //dd($result['data']->toArray());//

        return $result;
    }
//{@block_br/}

//{@block_r}
    /*
     * 根据 主键id 获取详情 - 模板
     */
    public function sampleRead(array $requestQuery, $id)
    {
        //业务逻辑
        //{@field_detail
        $fields = ['*'];
        //@field_detail}

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);

        //?key=value 范围查询
        $builder = SampleRepo::searchInstance($fields);
        $builder->queryMatchDetail($QM);

        //默认排序
        $builder->orderBy('updated_at', 'desc');

        if (!empty($id)) {
            $result = $builder->find($id);
        } else {
            $result = $builder->first();
        }

        //dd($result->toArray());//

        return $result;
    }
//{@block_r/}

//{@block_d}
    /*
     * 根据 主键id 删除详情 - 模板
     */
    public function sampleDelete($id)
    {
        //业务逻辑

        //软删除数据
        $builder = SampleRepo::newInstance();
        $result  = $builder->isExit($id);

        //软删除数据
        $result->delete();
        //恢复软删除数据
        //$builder->withTrashed()->where('id',$id)->restore();

        return $result;
    }
//{@block_d/}

//{@block_cmd}
    /*
     * 命令行 - 模板
     */
    public function taskCmd($param = null)
    {

        //dd($param);//
        return true;
    }
//{@block_cmd/}

}
