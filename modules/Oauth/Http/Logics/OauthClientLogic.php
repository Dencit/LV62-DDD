<?php

namespace Modules\Oauth\Http\Logics;

use Modules\Base\Logic\BaseLogic;
use Modules\Oauth\Jobs\OauthClientJob;
use Extend\Util\QueryMatch;
use Modules\Oauth\Reposit\OauthClientRepo;

/**
 * notes: 应用层-业务类
 * 说明: 业务类数据操作,一般不直接调用模型,通过仓储类提供存粹的数据执行函数, 跨 应用端/模块 操作同一数据类型的业务, 建议抽象到 领域层-业务类, 减少冗余.
 * 调用原则: 向下调用[仓储类,领域层-业务类]
 */
class OauthClientLogic extends BaseLogic
{


    /*
     * 新增数据 - 
     */
    public function oauthClientSave(&$requestInput)
    {
        //业务逻辑
        $builder = OauthClientRepo::newInstance($requestInput);
        $builder->saveOrFail();
        $result = $builder->fresh();

        return $result;
    }


//{@block_cj}
//{@block_cj/}

//{@block_bc}
//{@block_bc/}


    /*
     * 根据 主键id 更新详情 - 
     */
    public function oauthClientUpdate($id, &$requestInput)
    {
        //业务逻辑
        $builder = OauthClientRepo::searchInstance();
        $builder = $builder->isExit($id);

        $builder->fill($requestInput);
        $builder->saveOrFail();
        $result = $builder->fresh();

        return $result;
    }


//{@block_bu}
//{@block_bu/}


    /*
     * 列表筛选 - 
     */
    public function oauthClientIndex(array $requestQuery)
    {
        //业务逻辑
        //{@field_collect
		    $fields = ["id","scope_id","client_title","client_info","client_id","client_secret","type","status","created_at","updated_at","deleted_at"];
		    //@field_collect}

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);

        $builder = OauthClientRepo::searchInstance($fields);
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



    /*
     * 根据 主键id 获取详情 - 
     */
    public function oauthClientRead(array $requestQuery, $id)
    {
        //业务逻辑
        //{@field_detail
		    $fields = ["id","scope_id","client_title","client_info","client_id","client_secret","type","status","created_at","updated_at","deleted_at"];
		    //@field_detail}

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);

        //?key=value 范围查询
        $builder = OauthClientRepo::searchInstance($fields);
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



    /*
     * 根据 主键id 删除详情 - 
     */
    public function oauthClientDelete($id)
    {
        //业务逻辑

        //软删除数据
        $builder = OauthClientRepo::newInstance();
        $result  = $builder->isExit($id);

        //软删除数据
        $result->delete();
        //恢复软删除数据
        //$builder->withTrashed()->where('id',$id)->restore();

        return $result;
    }


//{@block_cmd}
//{@block_cmd/}

}
