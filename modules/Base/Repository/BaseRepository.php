<?php

namespace Modules\Base\Repository;

use Extend\Util\QueryMatch;

/**
 * notes: 数据单元仓储-基类
 * @author 陈鸿扬 | @date 2022/7/4 16:11
 */
class BaseRepository
{
    //继承类覆盖的模型class
    protected $model = null;
    //当前模型引用
    protected static $query = null;
    //当前单例
    protected static $instance = null;

    public function __construct(array $input = null, $callStatic = 0)
    {
        switch ($callStatic) {
            default: //引用 动态实例
                if (!empty($input)) {
                    self::$query = (new $this->model($input)); // new modelClass($input)
                } else {
                    self::$query = (new $this->model()); // new modelClass()
                }
                break;
            case 1: //引用 源模型 动态实例
                $fields      = $input;
                self::$query = ($this->model)::select($fields); // modelClass::select(['*'])
                break;
            case 2: //引用 源模型 静态类
                self::$query = $this->model; // modelClass::class
                break;
        }
    }

    //仓储单例 - 仓储模型 和 源模型 实例化, 不能调用间接实例化的函数.
    public static function newInstance(array $fieldData = null)
    {
        self::$instance = new static($fieldData, 0);
        return self::$instance;
    }

    //融合单例 - 仓储模型 和 源模型 融合, 共享函数(实例,静态,间接实例).
    public static function searchInstance(array $fields = ['*'])
    {
        if (!(self::$instance instanceof static)) {
            self::$instance = new static($fields, 1);
        }
        return self::$instance;
    }

    //源模型静态引用 - 仓储模型 和 源模型, 只能调用静态函数.
    public static function sourceInstance(array $fields = null)
    {
        if (!(self::$instance instanceof static)) {
            self::$instance = new static($fields, 2);
            return self::$instance;
        }
    }


    //转接不存在的动态函数 到 源模型上
    public function __call($name, $arguments)
    {
        if (!empty($arguments[0]) && !empty($arguments[1])) {
            return self::$query->{$name}($arguments[0], $arguments[1]);
        } else if (!empty($arguments[0])) {
            return self::$query->{$name}($arguments[0]);
        } else {
            return self::$query->{$name}();
        }
    }

    //转接不存在的静态函数 到 源模型上
    public static function __callStatic($name, $arguments)
    {
        //源模型静态引用
        self::sourceInstance();
        if (!empty($arguments[0]) && !empty($arguments[1])) {
            return self::$query::{$name}($arguments[0], $arguments[1]);
        } else if (!empty($arguments[0])) {
            return self::$query::{$name}($arguments[0]);
        } else {
            return self::$query::{$name}();
        }
    }

    public static function pageGet(QueryMatch $QM)
    {
        $query   = self::$query;
        $collect = [];

        $QM->pagination($per_page, $page, $pagination, $row);
        //dd($page, $per_page, $pagination, $row);//

        //克隆未翻页对象
        //执行步进翻页-获取翻页结果
        $collectArr      = $query->offset($row)->limit($per_page)->get();
        $collect['data'] = $collectArr;

        //打开翻页时,才有meta数据 且 计算总行数
        if ($pagination != 'false') {
            $meta['pagination'] = true;
            $meta['perpage']    = $per_page;
            $meta['page']       = $page;

            //最小化查表总计
            $tableCount = $query->select(['*'])->offset(0)->count();

            $meta['total_page'] = (int)ceil($tableCount / $per_page);
            $meta['total']      = $tableCount;

            $collect['meta'] = $meta;
        }

        //附加补充数据
        if (!empty($query->addMeta)) {
            if (!empty($collect['meta'])) {
                $collect['meta'] = array_merge($collect['meta'], $query->addMeta);
            } else {
                $collect['meta'] = $query->addMeta;
            }
        }

        return $collect;
    }

}