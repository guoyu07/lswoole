<?php
/**
 * Swoole Servers
 *
 * @author dupeng <dupeng@qufenqi.com>
 */
namespace Pauldo\Lswoole\Services;

use Pauldo\Lswoole\Services\Swoole\Contract;
use Log;

class Swoole implements Contract
{

    private $server;

    private $debug = false;

    public function __construct()
    {
        $this->debug = config('swoole.debug');
    }

    public function startServer($server_name)
    {
        $pidfile = $this->getPidFile();
        if ($pidfile) {
            $pid = file_get_contents($pidfile);
            if ($pid && posix_kill($pid, 0)) {
                throw new Exception("Server already started", 15);
            }
        }
        $config = config('swoole.servers.' . $server_name);
        if (empty($config)) {
            throw new Exception("No configure for {$server_name}");
        }
        $server_type = array_get($config, 'server_type', 'HttpServer');
        try {
            $serv = app(sprintf('Pauldo\Lswoole\Services\Swoole\%s', $server_type), ['ip' => $config['ip'], 'port' => $config['port']]);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
        if (empty($serv)) {
            throw new Exception("Create server faild!");
        }
        $this->server = $serv;
        $set_arr = array_except($config, ['ip', 'port', 'server_type']);
        $serv->set($set_arr);

        $this->bindEvents();
        $serv->bindEvents();

        $serv->start();
        return $serv;
    }

    public function stopServer($server_name)
    {
        $pidfile = $this->getPidFile();
        $pid = 0;
        if ($pidfile) {
            $pid = file_get_contents($pidfile);
            unlink($pidfile);
        }
        if (! $pid) {
            throw new Exception("Server {$server_name} is not running");
        }
        return posix_kill($pid, SIGTERM);
    }

    public function restartServer($server_name)
    {
        $pidfile = $this->getPidFile();
        $pid = file_get_contents($pidfile);
        if ($pid) {
            posix_kill($pid, SIGTERM);
        }
        return $this->startServer($server_name);
    }

    public function getRequireEvents()
    {
        return [
            'start'     => 'onStart',
            'task'      => 'onTask',
            'finish'    => 'onFinish',
            'connect'   => 'onConnect',
            'receive'   => 'onReceive',
        ];
    }

    public function bindEvents()
    {
        $events = $this->getRequireEvents();
        foreach ($events as $event => $function) {
            $this->server->on($event, [$this, $function]);
        }
    }

    /**
     * 回调事件, 请勿手动调用
     */
    public function onStart($serv)
    {
        Log::debug('onStart');
        // swoole_set_process_name($this->config['process_name']);
        // 记录进程pid
        Log::debug('pid:', [getmypid()]);
        $pid = getmypid();
        $this->savePidFile($pid);
    }

    public function onConnect($serv, $fd)
    {
        Log::debug('onConnect');
    }

    public function onReceive()
    {
        Log::debug('onReceive');
    }

    public function onTask()
    {
        Log::debug('onTask');
    }

    public function onFinish()
    {
        Log::debug('onFinish');
    }

    private function savePidFile($pid)
    {
        $file = $this->getPidFile();
        return file_put_contents($file, $pid);
    }

    private function getPidFile()
    {
        $path = config('swoole.pid_path', storage_path());

        $file = $path . '/' . 'test.pid';
        return $file;
    }

}
