<?php
/**
 * Created by PhpStorm.
 * User: yhl
 * Date: 2019/4/18
 * Time: 10:44
 */

namespace App\Process;


use App\Test\Test;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Utility\File;

class HotReload extends AbstractProcess
{
    protected $dir;

    public function run($arg)
    {
        $this->dir = empty($arg['dir']) ? EASYSWOOLE_ROOT . '/App' : $arg['dir'];
        if (extension_loaded('inotify') && empty($arg['disableInotify'])) {
            $this->reloadByInotify();
            echo 'reload by inotify' . PHP_EOL;
        } else {
            echo 'not use inotify' . PHP_EOL;
        }
    }


    protected function reloadByInotify()
    {
        global $resource;
        global $lastTime;
        $lastTime = 0;

        $resource = inotify_init();
        $files = File::scanDirectory($this->dir);
        $files = array_merge($files['files'], $files['dirs']);

        foreach ($files as $item) {
            inotify_add_watch($resource, $item, IN_CREATE | IN_MODIFY | IN_DELETE);
        }


        /** @var int $resource */
        swoole_event_add($resource, function () {
            global $resource;
            global $lastTime;
            $event = inotify_read($resource);
            if (!empty($event) && $lastTime < time()) {
                $lastTime = time();
                ServerManager::getInstance()->getSwooleServer()->reload();
            }
        });

    }

    public function onShutDown()
    {
        // TODO: Implement onShutDown() method.
    }

    public function onReceive(string $str)
    {
        // TODO: Implement onReceive() method.
    }

}