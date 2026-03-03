<?php

namespace Modules\Demo\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

/**
 * notes: 队列类
 * Class SampleJob
 * @package Modules\Demo\Jobs
 */
class SampleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle($requestInput)
    {
        Log::channel('queue')->notice('sample job end');
    }
}
