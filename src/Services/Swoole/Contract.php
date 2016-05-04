<?php
/**
 * Swoole Interface
 *
 * @author paul du <admin@pauldo.me>
 */
namespace Pauldo\Lswoole\Services\Swoole;

interface Contract
{
    /**
     * 必要的监听事件
     * @return array    ['request']
     */
    public function getRequireEvents();

    /**
     * 绑定事件回调
     * @return void
     */
    public function bindEvents();
}
