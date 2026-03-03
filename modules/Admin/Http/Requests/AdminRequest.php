<?php

namespace Modules\Admin\Http\Requests;

use Modules\Base\Request\BaseRequest;

/**
 * notes: 应用层-输入验证类
 * desc: 只在此类 统一校验输入数据.
 * 内置规则: https://laravelacademy.org/post/9547
 */
class AdminRequest extends BaseRequest
{
    //验证规则
    protected $rules = [
        //@rules
        "id"            => "integer|gt:0|between:0,20",
        "user_id"       => "integer|gt:0|between:0,20",
        "role"          => "string|between:0,255",
        "name"          => "string|between:0,255",
        "avatar"        => "string|between:0,255",
        "signature"     => "string|between:0,255",
        "gender"        => "integer|gt:0|between:0,3",
        "birthday"      => "string|between:0,10",
        "mobile"        => "string|between:0,30",
        "mail"          => "string|between:0,255",
        "qq"            => "string|between:0,255",
        "password"      => "string|between:0,255",
        "client_driver" => "string",
        "client_type"   => "integer|gt:0|between:0,3",
        "lat"           => "numeric|gt:0|between:0,10",
        "lng"           => "numeric|gt:0|between:0,10",
        "province"      => "string|between:0,255",
        "city"          => "string|between:0,255",
        "reg_method"    => "integer|gt:0|between:0,3",
        "reg_ip"        => "string|between:0,15",
        "login_method"  => "integer|gt:0|between:0,3",
        "login_ip"      => "string|between:0,15",
        "type"          => "integer|in:1,2",
        "status"        => "integer|in:1,2",
        "on_line_time"  => "date",
        "off_line_time" => "date",
        "created_at"    => "date",
        "updated_at"    => "date",
        "deleted_at"    => "date",
        //@rules
    ];

    //
    protected $messages = [
        //@messages
        'id.integer' => '主键 id 必须是整数',
        'id.gt'      => '主键 id 必须大于0',
        'id.gte'     => '主键 id 必须大于等于0',
        'id.max'     => '主键 id 超出最大值',
        'id.min'     => '主键 id 超出最小值',
        'id.in'      => '主键 id 数值超出许可范围',
        'id.between' => '主键 id 最大长度是 20',

        'user_id.integer' => '关联用户id 必须是整数',
        'user_id.gt'      => '关联用户id 必须大于0',
        'user_id.gte'     => '关联用户id 必须大于等于0',
        'user_id.max'     => '关联用户id 超出最大值',
        'user_id.min'     => '关联用户id 超出最小值',
        'user_id.in'      => '关联用户id 数值超出许可范围',
        'user_id.between' => '关联用户id 最大长度是 20',

        'role.string'     => '管理员角色 包含非法字符-只能是字符串',
        'role.alpha'      => '管理员角色 包含非法字符-只能是/字母',
        'role.alpha_num'  => '管理员角色 包含非法字符-只能是/字母/数字',
        'role.alpha_dash' => '管理员角色 包含非法字符',
        'role.between'    => '管理员角色 最大长度是 255',

        'name.string'     => '管理员名称 包含非法字符-只能是字符串',
        'name.alpha'      => '管理员名称 包含非法字符-只能是/字母',
        'name.alpha_num'  => '管理员名称 包含非法字符-只能是/字母/数字',
        'name.alpha_dash' => '管理员名称 包含非法字符',
        'name.between'    => '管理员名称 最大长度是 255',

        'avatar.string'     => '管理员头像 包含非法字符-只能是字符串',
        'avatar.alpha'      => '管理员头像 包含非法字符-只能是/字母',
        'avatar.alpha_num'  => '管理员头像 包含非法字符-只能是/字母/数字',
        'avatar.alpha_dash' => '管理员头像 包含非法字符',
        'avatar.between'    => '管理员头像 最大长度是 255',

        'signature.string'     => '签名 包含非法字符-只能是字符串',
        'signature.alpha'      => '签名 包含非法字符-只能是/字母',
        'signature.alpha_num'  => '签名 包含非法字符-只能是/字母/数字',
        'signature.alpha_dash' => '签名 包含非法字符',
        'signature.between'    => '签名 最大长度是 255',

        'gender.integer' => '性别 必须是整数',
        'gender.gt'      => '性别 必须大于0',
        'gender.gte'     => '性别 必须大于等于0',
        'gender.max'     => '性别 超出最大值',
        'gender.min'     => '性别 超出最小值',
        'gender.in'      => '性别 数值超出许可范围',
        'gender.between' => '性别 最大长度是 3',

        'birthday.string'     => '生日 包含非法字符-只能是字符串',
        'birthday.alpha'      => '生日 包含非法字符-只能是/字母',
        'birthday.alpha_num'  => '生日 包含非法字符-只能是/字母/数字',
        'birthday.alpha_dash' => '生日 包含非法字符',
        'birthday.between'    => '生日 最大长度是 10',

        'mobile.string'     => '手机 包含非法字符-只能是字符串',
        'mobile.alpha'      => '手机 包含非法字符-只能是/字母',
        'mobile.alpha_num'  => '手机 包含非法字符-只能是/字母/数字',
        'mobile.alpha_dash' => '手机 包含非法字符',
        'mobile.between'    => '手机 最大长度是 30',

        'mail.string'     => '邮箱 包含非法字符-只能是字符串',
        'mail.alpha'      => '邮箱 包含非法字符-只能是/字母',
        'mail.alpha_num'  => '邮箱 包含非法字符-只能是/字母/数字',
        'mail.alpha_dash' => '邮箱 包含非法字符',
        'mail.between'    => '邮箱 最大长度是 255',

        'qq.string'     => 'QQ号 包含非法字符-只能是字符串',
        'qq.alpha'      => 'QQ号 包含非法字符-只能是/字母',
        'qq.alpha_num'  => 'QQ号 包含非法字符-只能是/字母/数字',
        'qq.alpha_dash' => 'QQ号 包含非法字符',
        'qq.between'    => 'QQ号 最大长度是 255',

        'password.string'     => '密码 包含非法字符-只能是字符串',
        'password.alpha'      => '密码 包含非法字符-只能是/字母',
        'password.alpha_num'  => '密码 包含非法字符-只能是/字母/数字',
        'password.alpha_dash' => '密码 包含非法字符',
        'password.between'    => '密码 最大长度是 255',

        'client_driver.string'     => '客户端信息 包含非法字符-只能是字符串',
        'client_driver.alpha'      => '客户端信息 包含非法字符-只能是/字母',
        'client_driver.alpha_num'  => '客户端信息 包含非法字符-只能是/字母/数字',
        'client_driver.alpha_dash' => '客户端信息 包含非法字符',
        'client_driver.between'    => '客户端信息 超出最大长度 是65536',

        'client_type.integer' => '客户端类型 必须是整数',
        'client_type.gt'      => '客户端类型 必须大于0',
        'client_type.gte'     => '客户端类型 必须大于等于0',
        'client_type.max'     => '客户端类型 超出最大值',
        'client_type.min'     => '客户端类型 超出最小值',
        'client_type.in'      => '客户端类型 数值超出许可范围',
        'client_type.between' => '客户端类型 最大长度是 3',

        'lat.numeric' => '坐标 必须是数字或小数',
        'lat.gt'      => '坐标 必须大于0',
        'lat.gte'     => '坐标 必须大于等于0',
        'lat.max'     => '坐标 超出最大值',
        'lat.min'     => '坐标 低于最小值',
        'lat.in'      => '坐标 数值超出许可范围',
        'lat.between' => '坐标 最大长度是 10',

        'lng.numeric' => '坐标 必须是数字或小数',
        'lng.gt'      => '坐标 必须大于0',
        'lng.gte'     => '坐标 必须大于等于0',
        'lng.max'     => '坐标 超出最大值',
        'lng.min'     => '坐标 低于最小值',
        'lng.in'      => '坐标 数值超出许可范围',
        'lng.between' => '坐标 最大长度是 10',

        'province.string'     => '省 包含非法字符-只能是字符串',
        'province.alpha'      => '省 包含非法字符-只能是/字母',
        'province.alpha_num'  => '省 包含非法字符-只能是/字母/数字',
        'province.alpha_dash' => '省 包含非法字符',
        'province.between'    => '省 最大长度是 255',

        'city.string'     => '市 包含非法字符-只能是字符串',
        'city.alpha'      => '市 包含非法字符-只能是/字母',
        'city.alpha_num'  => '市 包含非法字符-只能是/字母/数字',
        'city.alpha_dash' => '市 包含非法字符',
        'city.between'    => '市 最大长度是 255',

        'reg_method.integer' => '注册方式 必须是整数',
        'reg_method.gt'      => '注册方式 必须大于0',
        'reg_method.gte'     => '注册方式 必须大于等于0',
        'reg_method.max'     => '注册方式 超出最大值',
        'reg_method.min'     => '注册方式 超出最小值',
        'reg_method.in'      => '注册方式 数值超出许可范围',
        'reg_method.between' => '注册方式 最大长度是 3',

        'reg_ip.string'     => '注册IP 包含非法字符-只能是字符串',
        'reg_ip.alpha'      => '注册IP 包含非法字符-只能是/字母',
        'reg_ip.alpha_num'  => '注册IP 包含非法字符-只能是/字母/数字',
        'reg_ip.alpha_dash' => '注册IP 包含非法字符',
        'reg_ip.between'    => '注册IP 最大长度是 15',

        'login_method.integer' => '最后登录方式 必须是整数',
        'login_method.gt'      => '最后登录方式 必须大于0',
        'login_method.gte'     => '最后登录方式 必须大于等于0',
        'login_method.max'     => '最后登录方式 超出最大值',
        'login_method.min'     => '最后登录方式 超出最小值',
        'login_method.in'      => '最后登录方式 数值超出许可范围',
        'login_method.between' => '最后登录方式 最大长度是 3',

        'login_ip.string'     => '最后登录IP 包含非法字符-只能是字符串',
        'login_ip.alpha'      => '最后登录IP 包含非法字符-只能是/字母',
        'login_ip.alpha_num'  => '最后登录IP 包含非法字符-只能是/字母/数字',
        'login_ip.alpha_dash' => '最后登录IP 包含非法字符',
        'login_ip.between'    => '最后登录IP 最大长度是 15',

        'type.integer' => '用户类型 必须是整数',
        'type.gt'      => '用户类型 必须大于0',
        'type.gte'     => '用户类型 必须大于等于0',
        'type.max'     => '用户类型 超出最大值',
        'type.min'     => '用户类型 超出最小值',
        'type.in'      => '用户类型 数值超出许可范围',
        'type.between' => '用户类型 最大长度是 3',

        'status.integer' => '启用状态 必须是整数',
        'status.gt'      => '启用状态 必须大于0',
        'status.gte'     => '启用状态 必须大于等于0',
        'status.max'     => '启用状态 超出最大值',
        'status.min'     => '启用状态 超出最小值',
        'status.in'      => '启用状态 数值超出许可范围',
        'status.between' => '启用状态 最大长度是 3',

        'on_line_time.date'        => '登录时间 日期时间格式有误',
        'on_line_time.date_format' => '登录时间 自定义日期格式有误',
        'on_line_time.required'    => '登录时间 不能为空',

        'off_line_time.date'        => '登出时间 日期时间格式有误',
        'off_line_time.date_format' => '登出时间 自定义日期格式有误',
        'off_line_time.required'    => '登出时间 不能为空',

        'created_at.date'        => '创建时间|注册时间 日期时间格式有误',
        'created_at.date_format' => '创建时间|注册时间 自定义日期格式有误',

        'updated_at.date'        => '更新时间 日期时间格式有误',
        'updated_at.date_format' => '更新时间 自定义日期格式有误',
        'updated_at.required'    => '更新时间 不能为空',

        'deleted_at.date'        => '删除时间 日期时间格式有误',
        'deleted_at.date_format' => '删除时间 自定义日期格式有误',
        'deleted_at.required'    => '删除时间 不能为空',

        //@messages
    ];

    //edit 验证场景 定义方法
    //例子: $this->only(['name','age']) ->append('name', 'min:5') ->remove('age', 'between') ->append('age', 'require|max:100');
    public function sceneSave()
    {
        return $this
            ->append('nickname', 'required')
            ->append('mobile', 'required')
            ->append('password', 'required');
    }

    public function sceneUpdate()
    {
        //return $this->append('id', 'required');
    }

    public function scenePatch()
    {
        //return $this->append('id', 'required');
    }

    public function sceneRead()
    {
        //return $this->append('id', 'required');
    }

    public function sceneDelete()
    {
        //return $this->append('id', 'required');
    }

}