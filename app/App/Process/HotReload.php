<?php
/**
 * Created by PhpStorm.
 * User: yhl
 * Date: 2019/4/18
 * Time: 10:44
 */

namespace App\Process;
use EasySwoole\Component\Process\AbstractProcess;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\Utility\File;
use Swoole\Table;
use Swoole\Timer;

class HotReload extends AbstractProcess
{
    protected $dir;
    /** @var \swoole_table $table */
    protected $table;
    protected $beta = false;

    public function run($arg)
    {
        $this->dir = empty($arg['dir']) ? EASYSWOOLE_ROOT . '/App' : $arg['dir'];

        if (extension_loaded('inotify') && empty($arg['disableInotify'])) {
            $this->reloadByInotify();
            echo 'reload by inotify' . PHP_EOL;
        } else {

            $this->table = new \swoole_table(1024);
            $this->table->column('mtime', Table::TYPE_INT);
            $this->table->create();

            Timer::tick(2000, function () {
                $this->reloadByTimer();
            });

            echo 'reload by timer tick' . PHP_EOL;
        }
    }


    protected function reloadByTimer()
    {
        //遍历所有的文件，用文件的inode号做id，存入最后更新时间，

        $dir = new \RecursiveDirectoryIterator($this->dir);
        /** @var \SplFileInfo[] $files $files */
        $files = new \RecursiveIteratorIterator($dir);
        $need = false;
        $all = [];
        foreach ($files as $item) {
            $node = $item->getInode();
            $mTime = $item->getMTime();
            $all[] = $node;
            if ($this->table->exist($node)) {
                //文件已经载入
                if ($this->table->get($node)['mtime'] != $mTime) {
                    //但是文件被修改过，也需要重新载入
                    $this->table->set($node, ['mtime' => $mTime]);
                    $need = true;
                }
            } else {
                //文件未载入，肯定需要重新载入
                $need = true;
                $this->table->set($node, ['mtime' => $mTime]);
            }
        }

        //当文件被删除，就清除记录
        foreach ($this->table as $key => $row) {
            if (!in_array($key, $all)) {
                $this->table->del($key);
                $need = true;
            }
        }

        //当文件发生变化，需要重载
        if ($need) {
            if($this->beta === true) {
                ServerManager::getInstance()->getSwooleServer()->reload();
                echo 'reload files by timer tick'.PHP_EOL;
            } else {
                echo 'not need reload in the start server'.PHP_EOL;
                $this->beta = true;
            }
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