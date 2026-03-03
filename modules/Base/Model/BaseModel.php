<?php

namespace Modules\Base\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Base\Error\BaseError;
use Modules\Base\Exception\Exception;

/**
 * notes: 数据单元模型-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class BaseModel extends Model
{
    //获取数据库字段
    public function getFieldKeys()
    {
        if (!empty($this->casts)) {
            //casts
            $fieldKeyArr = $this->getCasts();
        } else if (!empty($this->schema)) {
            //schema
            $fieldKeyArr = $this->schema;
        } else {
            //db fields
            $fieldKeyArr = Schema::getColumnListing ($this->getTable());
            if(!empty($fieldKeyArr)){
                $fieldKeyArr = array_flip($fieldKeyArr);
            }
        }
        $fieldKeys = array_keys($fieldKeyArr);
        return $fieldKeys;
    }

    /**
     * notes: 通过Select文本 count/sum 计算指定字段
     * @author 陈鸿扬 | @date 2022/1/12 11:16
     * @param $fieldStr - 计算的字段字符串
     * @return array 返回一行数据
     */
    public static function computeSelectRow($fieldStr){
        $query = self::selectRaw( $fieldStr );
        $result = $query->first();
        if($result){
            return  $result->toArray();
        }
        return [];
    }

    /**
     * notes: 通过Select文本 count/sum 计算指定字段 - group by 版本
     * @author 陈鸿扬 | @date 2022/1/13 11:02
     * @param $fieldStr - 计算的字段字符串
     * @param array $group - 多个聚合条件
     * @return array 返回多行数据
     */
    public static function computeGroupSelectRow($fieldStr, array $group = [])
    {
        $query = self::selectRaw( $fieldStr );

        if( empty($group) ){
            foreach ($group as $ind => $val){
                $query->groupBy($val);
            }
        }

        $result = $query->get();
        if($result){
            return  $result->toArray();
        }
        return [];
    }

    //批量新增
    public function addAll(Array $data){
        //foreach ($data as $i=>$d){ var_dump(count($d)); }
        $result = DB::table($this->getTable())->insert($data);
        return $result;
    }

    //批量更新 - 依靠主键id 或 唯一id
    public function updateAll($data, $field){
        $ids=[]; $upFieldArr=[];  $whenStrArr=[]; $updateSql="";
        if(!empty($data)){
            //获取 待赋值字段 集合
            foreach ($data[0] as $m=>$n){ if( $m == $field ){ continue; } $upFieldArr[]=$m; }
            //拼接 待赋值 query
            foreach ($data as $k=>$v){
                $ids[]= $v[$field];
                foreach ($upFieldArr as $ind=>$key ){
                    $whenStr = " WHEN '" . $v[$field] . "' THEN '" . $v[$key] . "'";
                    if( $k==0 ){
                        $whenStrArr[$key]= "`".$key."` = CASE ".$field." ".$whenStr;
                        if($k==count($data)-1 ){ $whenStrArr[$key].= " END "; }
                    }
                    else if( $k == count($data)-1 ){ $whenStrArr[$key].= $whenStr." END "; }
                    else{ $whenStrArr[$key].= $whenStr; }
                }
            }
            //合成 总query
            $whenStrArr = array_values($whenStrArr); $ids = "'".implode("','" , $ids)."'";
            $updateSql= "UPDATE `" .env("DB_PREFIX"). $this->getTable() . "` SET ";
            foreach ($whenStrArr as $ke=>$va){
                if( $ke == count($whenStrArr)-1 ){ $updateSql .= $va;}
                else{ $updateSql .= $va.", "; }
            }
            $updateSql.="WHERE `".$field."` IN(" . $ids . ")";
            //dd($updateSql);//
            $result= DB::update( DB::raw($updateSql) );
            //dd($result);//
            return $result;
        }
        return null;
    }

    //返回完整sql文本 - 不同于框架的toSql()
    public function scopeToFullSql(Builder &$builder)
    {
        $sql = $builder->toSql();
        $bindings = $builder->getBindings();

        //过滤%,防vsprintf报错
        $sqlReplace = str_replace('%', '#', $sql);
        //替换符号
        $sqlReplace = str_replace("?", "'%s'", $sqlReplace);
        $sqlReplace = str_replace("#Y-#m-#d", "Y-m-d", $sqlReplace);
        $sqlReplace = vsprintf($sqlReplace, $bindings);
        //复原%
        $fullSql = str_replace('#', '%', $sqlReplace);
        //格式化
        $fullSql =  \SqlFormatter::format($fullSql,false);

        return $fullSql;
    }

    /**
     * notes: 依据查询条件count,不同于框架的 Count - 有去重字段时,应该调用fullCount,才起到去重计量作用
     * @param Builder $builder
     * @return int
     * @author 陈鸿扬 | @date 2022/5/19 16:56
     */
    public function scopeFullCount(Builder &$builder)
    {
        $count = 0;
        $sql = $builder->toSql();
        $bindings = $builder->getBindings();

        //跨库sql处理
        $conn = $builder->getModel()->connection;
//        $db = $accountDbName = config('database.connections.'.$conn.'.database');
//        $table = $builder->getModel()->table;
//        $sql = str_replace($table, $db . '.' . $table, $sql);

        //在 子查询sql 外面 count
        $subSql = "select count(*) as aggregate from (" . $sql . ") as subTable";
        $query = DB::connection($conn)->select($subSql,$bindings);
        if ($query && isset($query[0]->aggregate)) {
            $count = $query[0]->aggregate;
        }
        return $count;
    }

    //?_where=status/0
    public function scope_Where( Builder $builder){
        $where = request()->get('_where');
        if( $where ){  $where=explode(',',$where);
            if( is_array($where) ){
                foreach ($where as $item){
                    $this->operator($item);
                    $whereArr[]=$item;
                    $builder->where($whereArr);
                }
            }
        }
    }
    //query ?_where=key/value 运算符转换
    protected function operator(&$item){
        preg_match('/^[\w\s]+(>\\/|<\\/|>|<|\\/|\\|).*$/i',$item,$m);
        //var_dump($m);die;//
        if( isset($m[1]) ){ $item=explode($m[1],$item); }
        if(is_array($item)){
            $item[2] = $item[1];//拷贝值到新位置,原位置准备存放运算符
            switch ($m[1]){ //当匹配到 运算符标记时
                default: $item[1]='='; break; case "/": $item[1]='='; break;
                case ">": $item[1]='>'; break; case "<": $item[1]='<'; break;
                case ">/": $item[1]='>='; break; case "</": $item[1]='<='; break;
                //like查询的处理
                case "|": $item[1]='like';
                    preg_match('/^\%/i',$item[2],$left);//匹配左边内容
                    preg_match('/\%$/i',$item[2],$right);//匹配右边内容
                    $item[2]= trim($item[2],'%');
                    if( isset($left[0]) && $left[0]=='%'){
                        $item[2]= '%%'.$item[2];
                    }else if( isset($right[0]) && $right[0]=='%'){
                        $item[2]= $item[2].'%%';
                    }else{
                        $item[2]= '%%'.$item[2].'%%';
                    }
                    //var_dump($item[2]);die;//
                    break;
            }
        }

        //检查 _where表达式 是否合法
        if( !is_array($item) ){
            Exception::http(BaseError::code('WHERE_SEARCH_OPERATOR_FAIL'),BaseError::msg('WHERE_SEARCH_OPERATOR_FAIL'));
        }

        //var_dump($item);die;//
        return $item;
    }


    //?_where_in=status/1,2,3
    public function scope_WhereIn( Builder $builder){
        $whereIn = request()->get('_where_in');
        if( $whereIn ){  $whereIn=explode('|',$whereIn);
            if( is_array($whereIn) ){
                foreach ($whereIn as $item){
                    $this->inOperator($item,$sortItem);
                    $builder->whereIn($item[0],$item[1]);
                }
            }
        }
    }
    //?_where_in_sort=status/1,2,3 //按id顺序返回结果
    public function scope_WhereInSort( Builder $builder){
        $whereIn = request()->get('_where_in_sort');
        if( $whereIn ){  $whereIn=explode('|',$whereIn);
            if( is_array($whereIn) ){
                foreach ($whereIn as $item){
                    $this->inOperator($item,$sortItem);
                    $builder->whereIn($item[0],$item[1]);
                    //按ids顺序排序
                    $rawStr = "FIND_IN_SET(".$sortItem[0].",'".$sortItem[1]."'".')';
                    $builder->orderByRaw($rawStr);
                }
            }
        }
    }
    //query ?_where_in=key/value,value,.. 运算符转换
    protected function inOperator(&$item,&$sortItem=null){
        preg_match('/^([\w\s]+)(\\/)(.*)$/i',$item,$m);
        //var_dump($m);die;//
        if( isset($m[1]) ){ $item=explode($m[1],$item); }
        if(is_array($item)){
            $values = explode(',',$m[3]);
            $values = array_unique($values);
            //$whereInArr=[$m[1],'in',implode(',',$values)];//
            $whereInArr=[$m[1],$values];//
            $item=$whereInArr;
            //返回排序结构
            $sortItem[0] = $m[1];
            $sortItem[1] = implode(',',$values);
        }

        //检查 _where_in表达式 是否合法
        if( !is_array($item) ){
            Exception::app(BaseError::code('WHERE_IN_SEARCH_OPERATOR_FAIL'),BaseError::msg('WHERE_IN_SEARCH_OPERATOR_FAIL'));
        }

        return $item;
    }

    //排序-可批量
    public function scope_Order( Builder $builder){
        $order = request()->get('_sort','-id');
        $orders = $this->sortOperator($order);
        foreach ( $orders as $k=>$v ){
            $builder->orderBy( $k,$v );
        }
    }
    //排序-可批量 - 无默认order id=desc 排序
    public function scope_Sort( Builder $builder){
        $order = request()->get('_sort');
        if(!empty($order)){
            $orders = $this->sortOperator($order);
            foreach ( $orders as $k=>$v ){
                $builder->orderBy( $k,$v );
            }
        }
    }
    //排序-sort参数转换
    protected function sortOperator($orderStr){
        $sortMap = explode(',',$orderStr); $sortArr=[];
        foreach ($sortMap as $ind=>$sortStr){
            $orderFields='id'; $orderType='desc';
            preg_match("/^(-|)(.*)$/i",$sortStr,$m);
            //var_dump($m);die;//
            if($m[0]){
                switch ($m[1]){ default: $orderType='asc'; break; case "-" : $orderType='desc'; break; }
                $orderFields=$m[2];
            }
            $sortArr[$orderFields]=$orderType;
        }
        return $sortArr;
    }

    //成组-可批量 - _group=id,userid
    public function scope_Group( Builder $builder){
        $groupFields = request()->get('_group');
        if(!empty($groupFields)){
            $groupArr = explode(',',$groupFields);
            foreach ( $groupArr as $ind=>$v ){
                $builder->groupBy( $v );
            }
        }
    }


    //自定义关联查询模型
    public function scope_Include( Builder $builder){
        $include= request()->get('_include');
        if( isset( $include ) && !empty( $include ) ){
            $joins=explode(',',$include);
            $this->incModelHave($joins);
            foreach ($joins as $ind=>$name){
                $methodName = $this->toHumpName($name);
                $methodExits = method_exists($this,$methodName);
                if($methodExits){ $builder->with($methodName); }
            }
        }
    }
    //检查关联查询模型
    public function incModelHave( &$joins ){
        foreach ($joins as $ind=>$name){
            $methodName = $this->toHumpName($name);
            $methodExis = method_exists($this, $methodName);
            if(!$methodExis){ unset($joins[$ind]); };
        }
    }
    //小写名称转驼峰 - 如 user_name : userName
    public function toHumpName($name){
        $nameArr = explode('_',$name); $newName = '';
        foreach ($nameArr as $ind=>$str ){
            if($ind==0){ $newName.=strtolower($str); }
            else{ $newName.=ucwords($str); }
        }
        return $newName;
    }

    //翻页查询 scope
    public function scope_Page( Builder $builder)
    {
        if( request()->get("_pagination")!='false' ){
            $perpage = (int) request()->get('_perpage',20);
            $page = (int) request()->get('_page',1);
        }else{
            //如果关闭翻页 最大翻页条数 上限到100
            $perpage = (int) request()->get('_perpage',100);
            $page = (int) request()->get('_page',1);
        }

        if($page<1){ $page=1; };
        $row = ($page-1)*$perpage;
        $builder->offset($row)->limit($perpage);
    }


    public static function pageAble( Builder &$builder , $addMeta = null ){
        //执行数据查询
        $collectArr= $builder->get();

        $collect=[];
        $collect['collect']=$collectArr;

        //打开翻页时 才有 meta 数据
        if( request()->get("_pagination")!='false' ){
            $meta['pagination']=true;
            $meta['perpage'] = (int) request()->get('_perpage',20);
            $meta['page'] = (int) request()->get('_page',1);

            //最小化查表总计
            $tableCount = $builder->select(['id'])->offset(0)->count();

            $meta['total_page'] = (int)ceil($tableCount/$meta['perpage']);
            $meta['total'] = $tableCount;

            $collect['meta'] = $meta;
        }

        //附加补充数据
        if(!empty($addMeta)){
            if( !empty( $collect['meta'] ) ){ $collect['meta'] = array_merge( $collect['meta'],$addMeta); }
            else{ $collect['meta'] = $addMeta; }
        }

        return $collect ;

    }

    //目标数据不为空
    public static function NoEmpty($data,$key){
        if( isset($data[$key]) && $data[$key]!='' ){ return true; }
        return false;
    }

}