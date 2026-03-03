<?php

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * notes: sql 日志监听
 * @author 陈鸿扬 | @date 2021/7/28 9:42
 * Class SqlListener
 * @package App\Listeners
 */
class SqlListener
{

    public function __construct()
    {
    }

    public function handle(QueryExecuted $query){

        $sql = $query->sql; $bindings = $query->bindings;
        //强制转文本
        //array_walk($bindings,function(&$key){ $key = (string) $key; });

        //过滤%,防vsprintf报错
        $sqlReplace = str_replace('%','#',$sql);
        //替换符号
        $sqlReplace = str_replace("?", "'%s'", $sqlReplace);
        $sqlReplace = str_replace("#Y-#m-#d","Y-m-d",$sqlReplace);
        $sqlReplace = vsprintf( $sqlReplace , $bindings );
        //复原%
        $sql = str_replace('#','%',$sqlReplace);

        if(config('app.env')=='online'){
            //折叠sql
            $sql =  \SqlFormatter::compress($sql);
        }else{
            //展开sql
            $sql =  \SqlFormatter::format($sql,false);
        }

        $info = "\n"."connection: ".$query->connectionName."\n";
        $info .= "execution_time: ".$query->time." ms;\n";

        $info .= "bindings: ".json_encode($bindings)."\n";
        $info .= 'sql: '.$sql."\n";

        Log::channel('sql')->notice($info);
    }


}
