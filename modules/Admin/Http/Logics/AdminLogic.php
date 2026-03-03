<?php

namespace Modules\Admin\Http\Logics;

use Extend\Cipher\AES;
use Modules\Base\Logic\BaseLogic;
use Modules\Oauth\Srv\OauthTokenSrv;
use Extend\Util\QueryMatch;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Errors\AdminRootError;
use Modules\Admin\Reposit\AdminRepo;
use Modules\Base\Exception\Exception;
use Modules\Oauth\Reposit\OauthClientRepo;
use Modules\Oauth\Reposit\OauthRoleRepo;
use Modules\User\Reposit\UserRepo;

/**
 * notes: 应用层-业务类
 * 说明: 业务类数据操作,一般不直接调用模型,通过仓储类提供存粹的数据执行函数, 跨 应用端/模块 操作同一数据类型的业务, 建议抽象到 领域层-业务类, 减少冗余.
 * 调用原则: 向下调用[仓储类,领域层-业务类]
 */
class AdminLogic extends BaseLogic
{

    /*
     * 管理-更新-登录
     */
    public function adminLogin($mobile, &$requestInput)
    {
        //检查权限
        $scopeId  = $requestInput['scope_id'];
        $clientId = $requestInput['client_id'];

        $OauthClientRepo = OauthClientRepo::searchInstance();
        $oauthClient     = $OauthClientRepo->isOauthClientExit($scopeId, $clientId);
        $clientSecret    = $oauthClient->client_secret;

        //业务逻辑
        $AdminRepo = AdminRepo::searchInstance();
        $builder   = $AdminRepo->isMobileExit($mobile);
        $adminId   = $builder->id;
        $adminRole = $builder->role;
        $userId    = $builder->user_id;

        //验证密码
        $AES                      = new AES(App("config")->get("app.key"));
        $pw_encrypt               = $AES->encrypt($requestInput['password']);
        $requestInput['password'] = $pw_encrypt;
        unset($requestInput['password']);
        if ($builder->password != $pw_encrypt) {
            Exception::http(AdminRootError::code("PASSWORD_WRONG"), AdminRootError::msg("PASSWORD_WRONG"), __METHOD__);
        }

        $now                          = date("Y-m-d H:i:s", time());
        $requestInput['on_line_time'] = $now;

        //自动事务函数
        $result = DB::transaction(function () use ($builder, $adminId, $adminRole, $userId, $scopeId, $clientId, $clientSecret, $requestInput) {

            //更新用户登录
            $builder->fill($requestInput);
            $builder->saveOrFail();
            $result = $builder->fresh();

            $extData = [
                'mobile' => $requestInput['mobile'],
            ];

            //生成 access_token ;
            if ($result) {

                $oauthInput = [
                    'scope_id' => $scopeId, 'client_id' => $clientId, 'client_secret' => $clientSecret,
                    'expire'   => 7200,
                ];
                //记录token
                $OauthTokenService = new OauthTokenSrv();
                $oauthToken        = $OauthTokenService->oauthTokenCreateByAdmin($adminId, $adminRole, $userId, $oauthInput, $extData);
                $result->id        = $adminId;
                $result->role      = $adminRole;
                $result->user_id   = $userId;
                $result->auth      = $oauthToken;
                return $result;
            }

            return false;
        });

        return $result;
    }

    /*
     * 管理员-更新-自己的详情
     */
    public function adminMeUpdate($adminId, &$requestInput)
    {
        //业务逻辑
        $AdminRepo = AdminRepo::searchInstance();
        $builder   = $AdminRepo->isExit($adminId);

        $builder->fill($requestInput);
        $builder->saveOrFail();
        $result = $builder->fresh();

        return $result;
    }


    /*
     * 管理-获取-自己的信息
     */
    public function adminMeDetail(array $requestQuery, $adminId)
    {
        //业务逻辑
        //{@field_detail
        $fields = ["id", "user_id", "role", "name", "avatar", "gender", "mobile", "password", "client_driver", "client_type",
            "lat", "lng", "status", "on_line_time", "off_line_time", "created_at", "updated_at", "deleted_at"];
        //@field_detail}

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);

        //?key=value 范围查询
        $builder = AdminRepo::searchInstance($fields);
        $builder->queryMatchDetail($QM);

        //默认排序
        $builder->orderBy('updated_at', 'desc');

        if (!empty($id)) {
            $result = $builder->find($adminId);
        } else {
            $result = $builder->first();
        }

        //dd($result->toArray());//

        return $result;
    }


    /*
     * 系统-新增-管理员
     */
    public function adminSysCreate(&$requestInput)
    {
        $AdminRepo = AdminRepo::searchInstance();
        $AdminRepo->isMobileUnique($requestInput['mobile']);

        $AES                           = new AES(App("config")->get("app.key"));
        $pw_encrypt                    = $AES->encrypt($requestInput['password']);
        $requestInput['password']      = $pw_encrypt;
        $requestInput['client_driver'] = '';

        //业务逻辑
        $builder = AdminRepo::newInstance($requestInput);
        $builder->saveOrFail();
        $result = $builder->fresh();

        return $result;
    }


    /*
     * 系统-获取-管理员列表
     */
    public function adminIndexForSys(array $requestQuery)
    {
        //业务逻辑

        //{@field_collect
        $fields = ["id", "user_id", "role", "name", "avatar", "gender", "mobile", "password", "client_driver", "client_type",
            "lat", "lng", "status", "reg_method", "on_line_time", "off_line_time", "created_at", "updated_at", "deleted_at"];
        //@field_collect}

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);

        $builder = AdminRepo::searchInstance($fields);
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
     * 系统-获取-管理员详情
     */
    public function adminSysDetail(array $requestQuery, $adminId)
    {
        //业务逻辑
        //{@field_detail
        $fields = ["id", "user_id", "role", "name", "avatar", "gender", "mobile", "password", "client_driver", "client_type",
            "lat", "lng", "status", "on_line_time", "off_line_time", "created_at", "updated_at", "deleted_at"];
        //@field_detail}

        //主表筛选逻辑-获取query查询表达式参数
        $QM = QueryMatch::instance($requestQuery);

        //?key=value 范围查询
        $builder = AdminRepo::searchInstance($fields);
        $builder->queryMatchDetail($QM);

        //默认排序
        $builder->orderBy('updated_at', 'desc');

        if (!empty($id)) {
            $result = $builder->find($adminId);
        } else {
            $result = $builder->first();
        }

        //dd($result->toArray());//

        return $result;
    }


    /*
     * 系统-更新-管理员详情
     */
    public function adminSysUpdate($adminId, &$requestInput)
    {
        //业务逻辑
        $AdminRepo = AdminRepo::searchInstance();
        $builder   = $AdminRepo->isExit($adminId);

        //检查用户
        if (isset($requestInput['user_id']) && $requestInput['user_id'] != 0) {
            $UserRepo = UserRepo::searchInstance();
            $UserRepo->isExit($requestInput['user_id']);
        }
        //检查角色
        if (isset($requestInput['role'])) {
            $OauthRoleRepo = OauthRoleRepo::searchInstance();
            $OauthRoleRepo->isRoleIdExit($requestInput['role']);
        }

        $builder->fill($requestInput);
        $builder->saveOrFail();
        $result = $builder->fresh();

        return $result;
    }


    /*
     * 系统-删除-管理员信息
     */
    public function adminSysDelete($id)
    {
        //业务逻辑

        //软删除数据
        $builder = AdminRepo::newInstance();
        $result  = $builder->isExit($id);

        //软删除数据
        $result->delete();
        //恢复软删除数据
        //$builder->withTrashed()->where('id',$id)->restore();

        return $result;
    }

}
