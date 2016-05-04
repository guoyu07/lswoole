<?php
/**
 * Swoole http_server
 *
 * @author paul du <admin@pauldo.me>
 */
namespace Pauldo\Lswoole\Services\Swoole;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use swoole_http_server;
use swoole_http_request;
use swoole_http_response;

class HttpServer extends swoole_http_server implements Contract
{

    public function __construct($ip, $port)
    {
        parent::__construct($ip, $port);
    }

    public function getRequireEvents()
    {
        return [
            'request'   => 'onRequest',
        ];
    }

    public function bindEvents()
    {
        $events = $this->getRequireEvents();
        foreach ($events as $event => $function) {
            $this->on($event, [$this, $function]);
        }
    }

    /**
     * 请求访问, 勿手动调用
     */
    public function onRequest(swoole_http_request $request, swoole_http_response $response)
    {
        $this->setGlobal($request);
        $app = require base_path('bootstrap/app.php');

        $res = $app->dispatch();
        if ($res instanceof SymfonyResponse) {
            $response->end($res->getContent());
        } else {
            is_string($res) ? $response->end($res) : $response->end();
        }
    }

    public function setGlobal(swoole_http_request $request)
    {
        $global_arr = [
            'get'       => '_GET',
            'post'      => '_POST',
            'files'     => '_FILES',
            'cookie'    => '_COOKIE',
            'server'    => '_SERVER',
        ];
        foreach ($global_arr as $skey => $globalname) {
            if (!empty($request->$skey)) {
                $$globalname = $request->$skey;
            } else {
                $$globalname = [];
            }
        }
        if (!empty($_SERVER)) {
            foreach ($_SERVER as $key => $value) {
                $_SERVER[strtoupper($key)] = $value;
            }
        }
        $_REQUEST = array_merge($_GET, $_POST, $_COOKIE);
        $_SERVER['REQUEST_METHOD'] = $request->server['request_method'];
        $_SERVER['REQUEST_URI'] = $request->server['request_uri'];
        $_SERVER['REMOTE_ADDR'] = $request->server['remote_addr'];
        foreach ($request->header as $key => $value) {
            $_key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
            $_SERVER[$_key] = $value;
        }
        \Illuminate\Http\Request::capture();
    }

}
