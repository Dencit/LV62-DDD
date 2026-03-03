<?php

namespace Modules\Media\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Base\Model\BaseModel;
/*
use Modules\User\Models\UserModel;
*/

/**
 * notes: 领域层-模型类
 * 说明: 负责基础层的工作,字段过滤(模型黑白名单),用户权限(模型策略),触发器(模型事件),等一系列传统DBA负责的工作.
 */
class MediaImageModel extends BaseModel
{
    use SoftDeletes;

    //为模型指定一个连接名称
    protected $connection = 'mysql';
    //与模型关联的数据表
    protected $table = 'media_images';

    //执行模型是否自动维护时间戳
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    //白名单: 指定哪些属性可以被集体赋值
    protected $fillable = [
        //@fillAble
		"host",
		"uri",
		"type",
		"status",
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
		"host"=>"string",
		"uri"=>"string",
		"type"=>"integer",
		"status"=>"integer",
		"created_at"=>"datetime",
		"updated_at"=>"datetime",
		"deleted_at"=>"datetime",
		//@casts
    ];

    protected $dates = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];

    //模型事件
    public static function boot(){
        parent::boot();

        //匿名的全局作用域
        static::addGlobalScope('base',function($query){
            //$query->where('is_delete',0);//默认获取-未软删除数据
        });

        //获取到模型实例后触发
        static::retrieved(function($input){ });
        //插入到数据库前触发
        static::creating(function($input){ });
        //插入到数据库后触发
        static::created(function($input){ });
        //更新到数据库前触发
        static::updating(function($input){ });
        //更新到数据库后触发
        static::updated(function($input){ });
        //保存到数据库前触发（插入/更新之前，无论插入还是更新都会触发）
        static::saving(function($input){ });
        //保存到数据库后触发（插入/更新之后，无论插入还是更新都会触发）
        static::saved(function($input){ });
        //从数据库删除记录前触发
        static::deleting(function($input){ });
        //从数据库删除记录后触发
        static::deleted(function($input){ });
        //恢复软删除记录前触发
        //static::restoring(function($input){ });
        //恢复软删除记录后触发
        //static::restored(function($input){ });

    }

    /*
    //user表关联
//    public function user(){
//        $fields = ['id','name'];
//        return $this->belongsTo(UserModel::class, 'id', 'id')->select($fields);
//    }
    */

}
