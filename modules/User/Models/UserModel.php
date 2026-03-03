<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Base\Model\BaseModel;
use Modules\Oauth\Models\OauthRoleModel;

/*
use Modules\User\Models\UserModel;
*/

/**
 * notes: 数据单元模型
 * Class UserModel
 * @package Modules\User\Models
 */
class UserModel extends BaseModel
{
    use SoftDeletes;

    //为模型指定一个连接名称
    protected $connection = 'mysql';
    //与模型关联的数据表
    protected $table = 'users';

    //执行模型是否自动维护时间戳
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    //白名单: 指定哪些属性可以被集体赋值
    protected $fillable = [
        //@fillAble
		"wx_openid",
		"qq_openid",
		"role",
		"name",
		"nickname",
		"avatar",
		"signature",
		"gender",
		"birthday",
		"mobile",
		"mail",
		"qq",
		"password",
		"im_password",
		"client_driver",
		"client_type",
		"lat",
		"lng",
		"province",
		"city",
		"reg_method",
		"reg_ip",
		"login_method",
		"login_ip",
		"type",
		"status",
		"on_line_time",
		"off_line_time",
		"remember_token",
		//@fillAble
    ];

    //黑名单:不被允许集体赋值
    protected $guarded = [
        //@guarded
		"id",
		"created_at",
		//@guarded
    ];

    //字段设置类型自动转换
    protected $casts = [
        //@casts
		"id"=>"integer",
		"wx_openid"=>"STRING",
		"qq_openid"=>"STRING",
		"role"=>"string",
		"name"=>"string",
		"nickname"=>"string",
		"avatar"=>"string",
		"signature"=>"string",
		"gender"=>"integer",
		"birthday"=>"STRING",
		"mobile"=>"STRING",
		"mail"=>"STRING",
		"qq"=>"STRING",
		"password"=>"string",
		"im_password"=>"string",
		"client_driver"=>"boolean",
		"client_type"=>"integer",
		"lat"=>"float",
		"lng"=>"float",
		"province"=>"string",
		"city"=>"string",
		"reg_method"=>"integer",
		"reg_ip"=>"STRING",
		"login_method"=>"integer",
		"login_ip"=>"STRING",
		"type"=>"integer",
		"status"=>"integer",
		"on_line_time"=>"datetime",
		"off_line_time"=>"datetime",
		"created_at"=>"datetime",
		"updated_at"=>"datetime",
		"deleted_at"=>"datetime",
		"remember_token"=>"string",
		//@casts
    ];

    protected $dates = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    //模型事件
    public static function boot()
    {
        parent::boot();

        //匿名的全局作用域
        static::addGlobalScope('base', function ($query) {
            //$query->where('is_delete',0);//默认获取-未软删除数据
        });

        //获取到模型实例后触发
        static::retrieved(function ($input) {
        });
        //插入到数据库前触发
        static::creating(function ($input) {
        });
        //插入到数据库后触发
        static::created(function ($input) {
        });
        //更新到数据库前触发
        static::updating(function ($input) {
        });
        //更新到数据库后触发
        static::updated(function ($input) {
        });
        //保存到数据库前触发（插入/更新之前，无论插入还是更新都会触发）
        static::saving(function ($input) {
        });
        //保存到数据库后触发（插入/更新之后，无论插入还是更新都会触发）
        static::saved(function ($input) {
        });
        //从数据库删除记录前触发
        static::deleting(function ($input) {
        });
        //从数据库删除记录后触发
        static::deleted(function ($input) {
        });
        //恢复软删除记录前触发
        //static::restoring(function($input){ });
        //恢复软删除记录后触发
        //static::restored(function($input){ });

    }


    //oauth_role表关联
    public function oauthRole()
    {
        $fields = ['*'];
        return $this->belongsTo(OauthRoleModel::class, 'role', 'role')->select($fields);
    }


}
