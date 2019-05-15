<?php
namespace app\wechat\home;

use app\index\controller\Home;
use EasyWeChat\Factory;

class Index extends Home
{
    protected $app;
    /**
     * Notes：微信入口文件(给微信访问的)
     * Author：张恩来<1059008079@qq.com>
     */
    public function index()
    {
        $config = module_config('wechat');
        $this->app = Factory::officialAccount($config);

        $this->app->server->push(function ($message) {
            if ($message['MsgType'] == 'event') {
                $class = '\\app\\wechat\\home\\Event';
                return call_user_func([new $class($message, $this->app), strtolower($message['Event'])]);
            } else {
                $class = '\\app\\wechat\\home\\Message';
                return call_user_func([new $class($message, $this->app), strtolower($message['MsgType'])]);
            }
        });
        $this->app->server->serve()->send();
    }
}