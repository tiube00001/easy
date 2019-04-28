<?php
/**
 * Created by PhpStorm.
 * User: yhl
 * Date: 2019/4/28
 * Time: 16:37
 */

namespace App\Process;

use EasySwoole\EasySwoole\Swoole\Task\QuickTaskInterface;

class TaskOne implements QuickTaskInterface
{
    static function run(\swoole_server $server, int $taskId, int $fromWorkerId, $flags = null)
    {
        // TODO: Implement run() method.
        var_dump(22222);
    }

}