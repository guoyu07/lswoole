## Lswoole

### swoole manager for lumen and laravel

### Installation

- composer install pauldo/lswoole
- cp vender/pauldo/lswoole/config/swoole.php config/
- Edit app/Console/Kernel.php
    add \Pauldo\Lswoole\Commands\SwooleCommand::class to $commands array
