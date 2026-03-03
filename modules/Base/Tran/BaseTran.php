<?php

namespace Modules\Base\Tran;

/**
 * notes: 数据单元输出转化器-公共类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class BaseTran
{
    //单例
    protected static $instance;
    //缓存requestQuery
    protected $requestQuery = [];
    //url ?_include = user,admin 获取到的 自定义关联模型名 集合
    protected $includeArr = [];

    //notes: 单例
    public static function instance()
    {
        if (!self::$instance instanceof static) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * notes: 输出数组-转化器 - 继承可覆盖 - 继承覆盖可自定义
     * @param $result
     * @return array
     * @author 陈鸿扬 | @date 2022/5/18 14:10
     */
    public function transformArray($result)
    {
        $data = [
            "id" => (int)$result['id'],
        ];
        return $data;
    }

    /**
     * notes: Model输出对象-转化器 - 继承覆盖可自定义
     * @param $result
     * @return array
     * @author 陈鸿扬 | @date 2022/5/18 14:11
     */
    public function transform($result)
    {
        $data = [
            "id" => (int)$result->id,
        ];
        //用 Model输出对象 获取字段范围, 过滤查询结果..
        $data = array_only($data, array_keys($result->getAttributes()));
        return $data;
    }

    /**
     * notes: 获取 自定义关联模型名 集合
     * @return $this
     * @author 陈鸿扬 | @date 2022/5/18 14:10
     */
    public function getIncludeArr()
    {
        //缓存requestQuery
        $this->requestQuery = request()->query();

        $includes = request()->get('_include');
        $this->includeArr = explode(',', $includes);
        return $this;
    }

    /**
     * notes: 设置 自定义关联模型名 集合
     * @param $includes
     * @return $this
     * @author 陈鸿扬 | @date 2022/5/18 14:10
     */
    public function setIncludeArr($includes)
    {
        $includeArr = explode(',', $includes);
        $this->includeArr = array_merge($this->includeArr, $includeArr);
        return $this;
    }

    /**
     * notes: 关联模型 belongsTo/hasMany 的 集成转化器
     * @param $itemResult
     * @param String $includeSign
     * @param string $transformClassName
     * @param string $methodName
     * @return array|bool|null
     * @author 陈鸿扬 | @date 2022/5/18 14:05
     */
    public function _include(& $itemResult, String $includeSign, string $transformClassName, string $methodName = 'transform')
    {
        //关联对象存在才处理
        if (isset($itemResult["$includeSign"])) {
            $temp = $itemResult["$includeSign"];
            $keys = array_keys($temp);
            $firstKey = $keys[0] ?? false;
            //一对一
            if (gettype($firstKey) == 'string') {
                $transform = $this->_includeBelongsTo($itemResult, $includeSign, $transformClassName, $methodName);
            }
            //一对多
            if (gettype($firstKey) == 'integer') {
                $transform = $this->_includeHasMany($itemResult, $includeSign, $transformClassName, $methodName);
            }
            if (!empty($transform)) {
                return $transform;
            }
            return null;
        }
    }

    /**
     * notes: 关联模型 belongsTo 的转化器
     * @param $itemResult - 主表模型返回对象 - 包含关联模型对象
     * @param $includeSign - 截取关联模型对象 - 自定义关联模型名称
     * @param $transformClassName - 转化器 类路径
     * @param string $methodName - 转化器 类方法
     * @return bool
     * @author 陈鸿扬 | @date 2022/3/30 11:46
     */
    public function _includeBelongsTo(& $itemResult, String $includeSign, string $transformClassName, string $methodName = 'transform')
    {
        if (in_array($includeSign, $this->includeArr) && !empty($itemResult)) {
            //关联到才执行
            $item = $itemResult["$includeSign"];
            if (!empty($item)) {
                $transform = (new $transformClassName)->{$methodName}($item);
                return $transform;
            }
        }

        return null;
    }

    /**
     * notes: 关联模型 hasMany 的转化器
     * @param $itemResult - 主表模型返回对象 - 包含关联模型对象
     * @param $includeSign - 截取关联模型对象 - 自定义关联模型名称
     * @param $transformClassName - 转化器 类路径
     * @param string $methodName - 转化器 类方法
     * @return array|bool
     * @author 陈鸿扬 | @date 2022/3/30 11:51
     */
    public function _includeHasMany(& $itemResult, String $includeSign, string $transformClassName, string $methodName = 'transform')
    {
        if (in_array($includeSign, $this->includeArr) && !empty($itemResult)) {
            $objects = [];
            $itemList = $itemResult["$includeSign"];
            foreach ($itemList as $item) {
                //关联到才执行
                if (!empty($item)) {
                    $transform = (new $transformClassName)->{$methodName}($item);
                    $objects[] = $transform;
                }
            }
            if (empty($objects)) {
                return false;
            }
            return $objects;
        }

        return null;
    }

    /**
     * notes: 单行数据转化
     * @param $itemData
     * @param string $methodName
     * @return mixed
     * @author 陈鸿扬 | @date 2022/5/18 14:05
     */
    public static function workItem(&$itemData, $methodName = 'transform')
    {
        $trans = new static();
        $trans->getIncludeArr();
        $currData = $trans->{$methodName}($itemData);
        return $currData;
    }

    /**
     * notes: 列表数据转化
     * @param $listData
     * @param string $methodName
     * @return array
     * @author 陈鸿扬 | @date 2022/5/18 14:05
     */
    public static function workList(&$listData, $methodName = 'transform')
    {
        $currData = [];
        $trans = new static();
        $trans->getIncludeArr();
        foreach ($listData as $index => $item) {
            $currData[] = $trans->{$methodName}($item);
        }
        return $currData;
    }

    /**
     * notes: 带翻页列表数据转化
     * @param $pageListData
     * @param string $methodName
     * @return mixed
     * @author 陈鸿扬 | @date 2022/5/18 14:05
     */
    public static function workPageList(&$pageListData, $methodName = 'transform')
    {
        $currData = [];
        $trans = new static();
        $trans->getIncludeArr();
        foreach ($pageListData['data'] as $index => $item) {
            $currData[] = $trans->{$methodName}($item);
        }
        $pageListData['data'] = $currData;
        return $pageListData;
    }

    /**
     * notes: 二级转化器输出结果 插到 一级数组列表指定字段后面.
     * @param $data
     * @param $pos
     * @param $addArr
     * @return array
     * @author 陈鸿扬 | @date 2022/5/18 14:03
     */
    public function dataAfterPush(&$data, $pos, $addArr)
    {
        if (is_string($pos)) {
            $kayIndexArr = array_flip(array_keys($data));
            $pos = $kayIndexArr[$pos] + 1;
        }
        $startArr = array_slice($data, 0, $pos);
        $startArr = array_merge($startArr, $addArr);
        $endArr = array_slice($data, $pos);
        $data = array_merge($startArr, $endArr);
        return $data;
    }


//v扩展函数区

    /*
     * notes: 转换/过滤 查询结果对象
     * @author 陈鸿扬 | @date 2021/3/9 14:23
     */
    public static function listObjTrans($data, $transformName = 'transform')
    {
        //数据对象 转 数组
        if (count($data) > 0 && gettype($data) == 'object' && gettype($data[0]) == 'object') {
            $newCollect = [];

            //初始化
            $instance = self::instance();
            //检查继承类中设置的转换函数
            $methodExist = method_exists($instance, $transformName);

            if ($methodExist) {
                foreach ($data as $k => $value) {
                    //获取最后继承类中,设置的转换函数
                    $newArr = $instance->{"$transformName"}($value);
                    //转换器可直接返回 null, 列表会跳过空数据.
                    if (!empty($newArr)) {
                        $newCollect[] = $newArr;
                    }
                }
            } else {
                foreach ($data as $k => $value) {
                    $newCollect[] = $value->toArray();
                }
            }

            return $newCollect;
        }

    }

    /*
     * notes: 转换/过滤 查询结果数组列表
     * @author 陈鸿扬 | @date 2021/3/12 19:53
     */
    public static function listArrTrans($data, $transformName = 'transform')
    {
        //数据对象 转 数组
        if (gettype($data) == 'array' && gettype($data[0]) == 'array') {
            $newCollect = [];

            //初始化
            $instance = self::instance();
            //检查继承类中设置的转换函数
            $methodExist = method_exists($instance, $transformName);

            if ($methodExist) {
                foreach ($data as $k => $value) {
                    //获取最后继承类中,设置的转换函数
                    $newArr = $instance->{"$transformName"}($value);
                    //转换器可直接返回 null, 列表会跳过空数据.
                    if (!empty($newArr)) {
                        $newCollect[] = $newArr;
                    }
                }
            } else {
                foreach ($data as $k => $value) {
                    $newCollect[] = $value;
                }
            }

            return $newCollect;
        }

    }

//^扩展函数区

}