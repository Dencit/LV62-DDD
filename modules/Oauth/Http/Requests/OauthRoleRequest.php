<?php

namespace Modules\Oauth\Http\Requests;

use Modules\Base\Request\BaseRequest;

/**
 * notes: 应用层-输入验证类
 * desc: 只在此类 统一校验输入数据.
 * 内置规则: https://laravelacademy.org/post/9547
 */
class OauthRoleRequest extends BaseRequest
{
    //验证规则
    protected $rules = [
        //@rules
		"id"=>"integer|gt:0|between:0,20",
		"scope_id"=>"string|between:0,255",
		"role"=>"string|between:0,255",
		"role_title"=>"string|between:0,255",
		"role_info"=>"string|between:0,255",
		"role_auths"=>"string|between:0,255",
		"type"=>"integer|in:1,2",
		"status"=>"integer|in:1,2",
		"created_at"=>"date",
		"updated_at"=>"date",
		"deleted_at"=>"date",
		//@rules
    ];

    //
    protected $messages = [
        //@messages
		'id.integer'=>'管理员角色 表id 必须是整数',
		'id.gt'=>'管理员角色 表id 必须大于0',
		'id.gte'=>'管理员角色 表id 必须大于等于0',
		'id.max'=>'管理员角色 表id 超出最大值',
		'id.min'=>'管理员角色 表id 超出最小值',
		'id.in'=>'管理员角色 表id 数值超出许可范围',
		'id.between'=>'管理员角色 表id 最大长度是 20',
		
		'scope_id.string'=>'授权范围-标记 包含非法字符-只能是字符串',
		'scope_id.alpha'=>'授权范围-标记 包含非法字符-只能是/字母',
		'scope_id.alpha_num'=>'授权范围-标记 包含非法字符-只能是/字母/数字',
		'scope_id.alpha_dash'=>'授权范围-标记 包含非法字符',
		'scope_id.between'=>'授权范围-标记 最大长度是 255',
		
		'role.string'=>'角色标记 包含非法字符-只能是字符串',
		'role.alpha'=>'角色标记 包含非法字符-只能是/字母',
		'role.alpha_num'=>'角色标记 包含非法字符-只能是/字母/数字',
		'role.alpha_dash'=>'角色标记 包含非法字符',
		'role.between'=>'角色标记 最大长度是 255',
		
		'role_title.string'=>'角色名称 包含非法字符-只能是字符串',
		'role_title.alpha'=>'角色名称 包含非法字符-只能是/字母',
		'role_title.alpha_num'=>'角色名称 包含非法字符-只能是/字母/数字',
		'role_title.alpha_dash'=>'角色名称 包含非法字符',
		'role_title.between'=>'角色名称 最大长度是 255',
		
		'role_info.string'=>'角色描述 包含非法字符-只能是字符串',
		'role_info.alpha'=>'角色描述 包含非法字符-只能是/字母',
		'role_info.alpha_num'=>'角色描述 包含非法字符-只能是/字母/数字',
		'role_info.alpha_dash'=>'角色描述 包含非法字符',
		'role_info.between'=>'角色描述 最大长度是 255',
		
		'role_auths.string'=>'角色权限表id集 包含非法字符-只能是字符串',
		'role_auths.alpha'=>'角色权限表id集 包含非法字符-只能是/字母',
		'role_auths.alpha_num'=>'角色权限表id集 包含非法字符-只能是/字母/数字',
		'role_auths.alpha_dash'=>'角色权限表id集 包含非法字符',
		'role_auths.between'=>'角色权限表id集 最大长度是 255',
		
		'type.integer'=>'角色类型 必须是整数',
		'type.gt'=>'角色类型 必须大于0',
		'type.gte'=>'角色类型 必须大于等于0',
		'type.max'=>'角色类型 超出最大值',
		'type.min'=>'角色类型 超出最小值',
		'type.in'=>'角色类型 数值超出许可范围',
		'type.between'=>'角色类型 最大长度是 3',
		
		'status.integer'=>'启用状态 必须是整数',
		'status.gt'=>'启用状态 必须大于0',
		'status.gte'=>'启用状态 必须大于等于0',
		'status.max'=>'启用状态 超出最大值',
		'status.min'=>'启用状态 超出最小值',
		'status.in'=>'启用状态 数值超出许可范围',
		'status.between'=>'启用状态 最大长度是 3',
		
		'created_at.date'=>'创建时间 日期时间格式有误',
		'created_at.date_format'=>'创建时间 自定义日期格式有误',
		
		'updated_at.date'=>'更新时间 日期时间格式有误',
		'updated_at.date_format'=>'更新时间 自定义日期格式有误',
		'updated_at.required'=>'更新时间 不能为空',
		
		'deleted_at.date'=>'删除时间 日期时间格式有误',
		'deleted_at.date_format'=>'删除时间 自定义日期格式有误',
		'deleted_at.required'=>'删除时间 不能为空',
		
		//@messages
    ];

    //edit 验证场景 定义方法
    //例子: $this->only(['name','age']) ->append('name', 'min:5') ->remove('age', 'between') ->append('age', 'require|max:100');
    public function sceneSave(){
        //return $this->append('id','required');




    }
    public function sceneUpdate(){
        //return $this->append('id', 'required');
    }
    public function sceneRead(){
        //return $this->append('id', 'required');
    }
    public function sceneDelete(){
        //return $this->append('id', 'required');
    }

}