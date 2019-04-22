<?php
/**
 * Created by PhpStorm.
 * User: yhl
 * Date: 2019/4/22
 * Time: 10:02
 */

namespace App\WebSocket\Controller;

use function Couchbase\defaultDecoder;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Socket\AbstractInterface\Controller;
use EasySwoole\Socket\Client\WebSocket;

class Index extends Controller
{
    function index()
    {
        $arg =$this->caller()->getArgs();
        $server = ServerManager::getInstance()->getSwooleServer();
        /** @var WebSocket $client */
        $client = $this->caller()->getClient();
        $fdList = $server->connections;
        $list = [];
        foreach ($fdList as $fd) {
            $list[] = $fd;
            if ($fd != $client->getFd()) {
                $server->push($fd, json_encode($arg, JSON_UNESCAPED_UNICODE));
            }
        }
        var_dump(json_encode($list));
    }
}