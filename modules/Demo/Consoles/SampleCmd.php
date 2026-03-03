<?php
namespace Modules\Demo\Consoles;

use Illuminate\Support\Facades\Log;
use Modules\Base\Console\BaseCmd;
use Modules\Demo\Srv\SampleSrv;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * notes: 数据单元指令类
 * doc: https://laravelacademy.org/post/9562
 * Class SampleCmd
 * @package Modules\Demo\Consoles
 */
class SampleCmd extends BaseCmd
{
    protected $name = 'cmd:sample';
    protected $description = 'Sample Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    //参数传递, 例子: name
    protected function getArguments()
    {
        return [
            ['date', InputArgument::OPTIONAL, 'date Y-m-d 指定日期.'],
        ];
    }
    //设置传递, 例子: --name 123
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, '--example 123', null],
        ];
    }

    //执行命令
    public function handle()
    {
        $SampleService = new SampleSrv();
        try {
            //命令行 业务逻辑
            $date = $this->argument('date');
            if(!empty($date)){
                $result = $SampleService->taskCmd($date);
            }else{
                $result = $SampleService->taskCmd();
            }
        }catch (\Exception $e){
            Log::channel('task')->error($e->getMessage());throw $e;
        }

        // 指令输出
        if($result){
            Log::channel('task')->notice('sample cmd ok');
            $this->notice( 'sample cmd ok' );
        }else{
            Log::channel('task')->notice('sample cmd end');
            $this->notice( 'sample cmd end' );
        }
    }
}
