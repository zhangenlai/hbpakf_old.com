<?php
namespace app\wechatsma\home;

use app\index\controller\Home;
use EasyWeChat\Factory;
use think\Db;
use think\Session;
use app\user\model\User;

class Index extends Home
{
    protected $app;
    public function __construct()
    {
        parent::__construct();
        $wechatConfig = [
            'app_id' => 'wx1459563402daf867',
            'secret' => '5b8fd94b58254b293461b98e35c03e61',
            'token' => 'zhangToken',
        ];
        $this->app = Factory::miniProgram($wechatConfig);
    }

    /**
     * Notes：微信入口文件(给微信访问的)
     * Author：张恩来<1059008079@qq.com>
     */
    public function index()
    {
        $options = [
            'log' => [
                'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
                'channels' => [
                    // 测试环境
                    'dev' => [
                        'driver' => 'single',
                        'path' => '../runtime/log/wechatsma/easywechat.log',
                        'level' => 'debug',
                    ],
                    // 生产环境
                    'prod' => [
                        'driver' => 'daily',
                        'path' => './runtime/log/wechatsma/easywechat.log',
                        'level' => 'debug',
                    ],
                ],
            ],
        ];
//        $wechatConfig = module_config('wechat');
//        $wechatConfig = array_merge($wechatConfig,$options);
        $this->app->server->serve()->send();
    }

    /**
     * Notes：小程序登录code
     * Author：张恩来<1059008079@qq.com>
     */
    public function getCode($code =null)
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (empty($data)) return 1;       //数据为空返回 1 错误码
            $result = $this->app->auth->session((string)$data['code']);

            $result['session_keyData'] = md5($result['openid']);
            return $result;
        }
    }


    /**
     * 获取用户基本信息  (存到后台数据库)
     */
    public function getUserInfo()
    {
        if ($this->request->isPost()){
            $data = $this->request->post();
            $sessionKey = $this->getCode($data['code'])['session_key'];
            //解密数据
            $data = [
                'session_key' => $sessionKey,
                'details' => [
                    'iv' => $data['res']['iv'],
                    'encryptedData' => $data['res']['encryptedData'],
                ]
            ];
            $result = $this->getEncData($data);
            $data = json_decode($result,true);
            if (empty($data)){
                return $result;
            }

            $userData = [
                'openidMini' => $data['openId'],
                'username' => $data['nickName'],
                'nickname' => $data['nickName'],
                'password' => '$2y$10$Brw6wmuSLIIx3Yabid8/Wu5l8VQ9M/H/CG3C9RqN9dUCwZW3ljGOK', //admin
                'avatar' => $data['avatarUrl'],
                'sex' => $data['gender'],
                'city' => $data['city'],
                'country' => $data['country'],
                'province' => $data['province'],
                'language' => $data['language'],
                'role' => 3, //小程序用户
                'status' => 1,
            ];

            //如果存在unionid的话
            if (!empty($user['unionid'])){
                $userData['unionid'] = $user['unionid'];
            }
            $userModel = new User();
            $userData['signup_ip'] = $userModel->setSignupIpAttr();
            //查询是否已存在
            if (!empty($user = User::get(['openidMini'=>$data['openId']]))){
                $user->isUpdate(true)->save($userData);     //更新
            }else {
                User::create($userData);    //新增
            }


            return json_encode($userData);
        }
    }

    /**
     * Notes：消息解密获取手机号
     * Author：张恩来<1059008079@qq.com>
     */
    public function getPhone()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (empty($data)) return 1;       //数据为空返回 1 错误码
            $result = $this->getEncData($data);     //获取解密数据

            return $result;
        }
    }

    /**
     * Notes：消息解密
     * Author：张恩来<1059008079@qq.com>
     * 格式  session_key || code   data['details']['iv'] && data['details']['encryptedData']
     */
    public function getEncData($data=null)
    {
        $data = $data ? $data : $this->request->post();
        if (empty($data['session_key']) && !empty($data['code'])){
            $data['session_key'] = $this->app->auth->session((string)$data['code'])['session_key'];
        }
        //解密数据
        $result = $this->app->encryptor->decryptData($data['session_key'],$data['details']['iv'],$data['details']['encryptedData']);

        return json_encode($result);
    }
}