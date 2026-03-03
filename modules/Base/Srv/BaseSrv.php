<?php

namespace Modules\Base\Srv;

/**
 * notes: 数据单元服务-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class BaseSrv {

    //统计用
    protected $itemFiled = null;//统计模板字段集
    //设置模板字段集 - 继承类可覆写
    protected function setItemField($statisticsDate){
        $this->itemFiled = [
            'statistics_date'=>$statisticsDate,
            'create_time'=> date('Y-m-d H:i:s',time()),'update_time'=> date('Y-m-d H:i:s',time()),
        ];
        return $this->itemFiled;
    }

    //设置模板字段集 - 传参版
    protected function initItemField($fieldArr){
        $this->itemFiled = $fieldArr;
        return $this->itemFiled;
    }

    /*
     * notes: 根据Y-m-d日期 生成统计区间
     * @author 陈鸿扬 | @date 2021/2/5 17:24
     */
    public function staticDateTime(&$startTime,&$endTime,&$currentDate=null,$date=null){
        if(!empty($date)){ //有date值 计算当天到23:59:59 的数据
            $currentDate = date('Y-m-d H:i:s',strtotime( $date.'' ));
            $startTime = date('Y-m-d H:i:s',strtotime( $date.'' ));
            $endTime = date('Y-m-d H:i:s',strtotime( $date.' +23 hour +59 minute +59 sec' ));
        }else{ //无date值 计算昨天到23:59:59 的数据
            $currentDate = date('Y-m-d H:i:s',strtotime( date('Y-m-d').' -1 day' ) );
            $startTime = date('Y-m-d H:i:s',strtotime( date('Y-m-d').' -1 day' ));
            $endTime = date('Y-m-d H:i:s',strtotime( date('Y-m-d').' -1 sec' ));
        }
        //var_dump($currentDate); var_dump($startTime); dd($endTime);//
    }
    /*
 * notes: 处理日期--根据Y-m-d日期 生成统计区间
 */
    public function dateControl(&$startTime,&$endTime){
        $startTime = date('Y-m-d H:i:s',strtotime( $startTime.'' ));
        $endTime = date('Y-m-d H:i:s',strtotime( $endTime.' +23 hour +59 minute +59 sec' ));
    }
    /*
     * notes: 把另一个列数据,通过$relationId,关联到准备提交的列表数据, 相应地添加数据 或 对重复数据进行更新
     * @author 陈鸿扬 | @date 2021/2/24 14:12
     */
    protected function combineList(&$beAddList,$getList,$relationId,$updateKeyArr=null){
        //获取准备添加的数据列表 $addIds
        $addIds = array_column($beAddList,"$relationId");
        $newAddData = [];
        //检查待添加数据是否重复
        if(!empty($getList)) {
            foreach ($getList as $ind => $item) {
                $searchIndex = array_search($item["$relationId"], $addIds);
                //重复则更新
                if ($searchIndex !== false) {
                    //往当前行数据-更新指定内容
                    $addIndex = $beAddList[$searchIndex];
                    $itemData = $this->itemData($addIndex, $relationId, $item, $updateKeyArr);
                    $beAddList[$searchIndex] = $itemData;
                }
                //不重复则添加
                else {
                    //新增行数据-添加指定内容
                    $addIndex = [];
                    $itemData = $this->itemData($addIndex,$relationId, $item, $updateKeyArr);
                    $newAddData[] = $itemData;
                }
            }
            //合并补充数据
            $beAddList = array_merge($beAddList, $newAddData);
            return $beAddList;
        }
    }
    /*
     * notes: 指定更新内容,用于单列数据更新
     * @author 陈鸿扬 | @date 2021/2/24 14:12
     */
    protected function itemData($addIndex,$relationId,$item,$updateKeyArr){
        //填充当前数据行没有的字段
        if( !empty($this->itemFiled) ){ $temple = $this->itemFiled;
            $addIndex = array_merge($temple, $addIndex);//$addIndex 覆盖 $temple
        }
        //同步当前数据行
        $itemData = $addIndex;
        //按设置字段名更新
        if( !empty($updateKeyArr)){
            $itemData["$relationId"] = $item["$relationId"];
            foreach ($updateKeyArr as $k){
                $itemData["$k"]=$item["$k"];
            }
        }
        //即使没有设置字段名,也要更新关联id
        else{
            $itemData["$relationId"] = $item["$relationId"];
        }
        return $itemData;
    }

    /*
     * notes: 获取两个数组列表的交集,并通过闭包 做数学计算 或 自定义行格式.
     * @author 陈鸿扬 | @date 2021/8/12 15:02
     */
    public function intersectListClosure($beAddList,$getList,$relationId,\Closure $closure ,\Closure $outClosure=null )
    {
        $newAddData = [];
        //两者非空时执行
        if( !empty($beAddList) && !empty($getList) ) {

            $getIds = array_column($getList,"$relationId");
            //检查待添加数据是否重复
            foreach ($beAddList as $beInd => $beItem) {
                //命中的列表上标
                $getIndex = array_search($beItem["$relationId"], $getIds);
                $itemData = [];
                //获取交叉行 - 由闭包自定义
                if( $getIndex !=false ) {
                    $getItem = $getList["$getIndex"];
                    $itemData = $closure($beInd,$beItem,$getIndex,$getItem,$relationId);
                }
                //返回行
                if( !empty($itemData) ){ $newAddData[] = $itemData; }
            }
            //只返回交叉行
            $beAddList = $newAddData;
            return $beAddList;

        }
        //其它情况
        else if ( !empty($beAddList) ){

            if( !empty($outClosure) ) {
                foreach ($beAddList as $beInd => $beItem) {
                    $itemData = $outClosure($beInd, $beItem, $relationId);
                    //返回行
                    if (!empty($itemData)) {
                        $newAddData[] = $itemData;
                    }
                }
                $beAddList = $newAddData;
                return $beAddList;
            }

        }

    }

    /*
     * notes: 筛选列表-提取包含的行 - 相当于 where in array list
     * @author 陈鸿扬 | @date 2021/8/11 15:15
     */
    protected function extractList($beExcludeList,$fieldKey, array $values)
    {
        $newList=[];
        foreach ($beExcludeList as $ind=>$data ){
            //字段存在 && 字段值 包含在筛选条件内
            if( isset($data["$fieldKey"]) && in_array($data["$fieldKey"],$values)  ){
                $newList[]=$data;
            }
        }
        return $newList;
    }

    //#



    //业务用

    //合计查询结果中的字段
    protected function fieldCollectSum($tempData,array $sumFields){
        $tempMeta = [];
        if( count($tempData)>0 ){
            foreach ($sumFields as $key=>$option){
                $valueArr = array_column($tempData,$key);
                switch ( $option ){
                    default: //相加
                        $tempMeta[$key] = array_sum($valueArr);
                        break;
                    case 'per' : //相加+均除
                        $countValue = count($valueArr);
                        $tempMeta[$key] = floatval( bcdiv(array_sum($valueArr),$countValue, 2) );
                        break;
                }
            }
        }
        return $tempMeta;
    }

    //#


}
