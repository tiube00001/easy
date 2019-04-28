<?php
/**
 * Created by PhpStorm.
 * User: yhl
 * Date: 2019/4/22
 * Time: 10:02
 */

namespace App\WebSocket\Controller;

use App\Process\TaskOne;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket;

class Index extends Controller
{
    function index()
    {

        $arg =$this->caller()->getArgs();
        /** @var WebSocket $client */
        $client = $this->caller()->getClient();
        $self = $client->getFd();
        TaskManager::async(TaskOne::class);
        TaskManager::async(function () use ($arg, $self) {
            $server = ServerManager::getInstance()->getSwooleServer();
            $fdList = $server->connections;
            $list = [];
            foreach ($fdList as $fd) {
                $list[] = $fd;
                if ($fd != $self) {
                    $server->push($fd, json_encode($arg, JSON_UNESCAPED_UNICODE));
                }
            }
            var_dump(json_encode($list, JSON_UNESCAPED_UNICODE));
        });

    }
}