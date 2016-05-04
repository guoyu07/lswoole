<?php

namespace Pauldo\Lswoole\Commands;

use Illuminate\Console\Command;
use Pauldo\Lswoole\Services\Swoole as SwooleService;
use Pauldo\Lswoole\Services\Exception;

class SwooleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lswoole:server {server_name=default} {op=start}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swoole Manager';

    private $swooleService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $server_name = $this->argument('server_name');
        $op = $this->argument('op');
        $this->info(sprintf('Swoole Manager is %sing %s server', $op, $server_name));
        try {
            
            if ('all' == $server_name) {
                switch ($op) {
                    case 'start':
                        // Start all swoole servers
                        break;
                    case 'stop':
                        break;
                    case 'restart':
                        break;
                    default:
                        break;
                }
            } else {
                switch ($op) {
                    case 'start':
                        $this->startServer($server_name);
                        break;
                    case 'stop':
                        $this->stopServer($server_name);
                        break;
                    case 'restart':
                        $this->restartServer($server_name);
                        break;
                    default:
                        break;
                }
            }

            // TODO: when start info not comming
            $this->info(sprintf('Server %s %s successfully', $server_name, $op));

        } catch (Exception $e) {
            $this->error(sprintf('Swoole Manager Error: %s [#%d]', $e->getMessage(), $e->getCode()));
        }

    }

    /**
     * Start a swoole_server
     */
    public function startServer($server_name)
    {
        $server = $this->getSwooleService()->startServer($server_name);
    }

    /**
     * Stop a swoole_server
     */
    public function stopServer($server_name)
    {
        $server = $this->getSwooleService()->stopServer($server_name);
    }

    /**
     * Restart a swoole_server
     */
    public function restartServer($server_name)
    {
        $server = $this->getSwooleService()->restartServer($server_name);
    }

    private function getSwooleService()
    {
        if (empty($this->swooleService)) {
            $this->swooleService = new SwooleService();
        }
        return $this->swooleService;
    }

}
