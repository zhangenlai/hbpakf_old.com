<?php
namespace app\wechat\home;

use app\index\controller\Home;
use EasyWeChat\Factory;
use Symfony\Component\Cache\Simple\RedisCache;

class Index extends Home
{
    protected $app;
    /**
     * Notes：微信入口文件(给微信访问的)
     * Author：张恩来<1059008079@qq.com>
     */
    public function index()
    {
//        // 创建 redis 实例
//        $redis = new Redis();
//        $redis->connect('redis_host', 6379);
//        // 创建缓存实例
//        $cache = new RedisCache($redis);
//        // 替换应用中的缓存
//        dump($this->app->rebind('cache', $cache));


        $options = [
            'log' => [
                'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver' => 'single',
                        'path' => '../runtime/log/wechat/easywechat.log',
                        'level' => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver' => 'daily',
                        'path' => './runtime/log/wechat/easywechat.log',
                        'level' => 'debug',
                        ],
                    ],
                ],
            ];
        $wechatConfig = module_config('wechat');
        $wechatConfig = array_merge($wechatConfig,$options);
        $this->app = Factory::officialAccount($wechatConfig);
        //发送模板消息（测试）
//            $res = $this->app->template_message->send([
//                'touser' => 'user-openid',
//                'template_id' => 'template-id',
//                'url' => 'https://easywechat.org',
//                'miniprogram' => [
//                    'appid' => 'xxxxxxx',
//                    'pagepath' => 'pages/xxx',
//                ],
//                'data' => [
//                    'key1' => 'VALUE',
//                    'key2' => 'VALUE2'
//            ],
//        ]);
//
//        dump($res);die;
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