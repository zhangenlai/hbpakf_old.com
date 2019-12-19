<?php

namespace app\wechat\home;

use app\index\controller\Home;
use app\wechat\model\WeMaterial;
use app\wechat\model\WeReply;
use app\user\model\User;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messagess\Media;

/*
 * 处理接收到的除事件消息
 * 方法名全部小写
 */
class Event extends Home
{
    protected $message;
    protected $app;

    public function __construct($message, $app)
    {
        $this->message = $message;
        $this->app = $app;
    }

    /**
     * Notes：关注事件
     * Author：张恩来<1059008079@qq.com>
     */
    public function subscribe()
    {
        $openid = $this->message['FromUserName'];
        $user = $this->app->user->get($openid);
        $userData = [
            'openid' => $openid,
            'username' => $user['nickname'],
            'nickname' => $user['nickname'],
            'password' => '$2y$10$Brw6wmuSLIIx3Yabid8/Wu5l8VQ9M/H/CG3C9RqN9dUCwZW3ljGOK', //admin
            'avatar' => $user['headimgurl'],
            'sex' => $user['sex'],
            'city' => $user['city'],
            'country' => $user['country'],
            'province' => $user['province'],
            'language' => $user['language'],
            'subscribe_scene' => $user['subscribe_scene'],
            'subscribe_state' => 0,
            'role' => '2', //公众号用户
            'status' => 1,
        ];
        //如果存在unionid的话
        if (!empty($user['unionid'])){
            $userData['unionid'] = $user['unionid'];
        }
        $userModel = new User();
        $userData['signup_ip'] = $userModel->setSignupIpAttr();
        //查询是否已存在
        if (!empty($user = User::get(['openid'=>$openid]))){
            $user->isUpdate(true)->save($userData);     //更新
        }else {
            User::create($userData);    //新增
        }
        //自动回复
        $data = $this->needAutoReply('subscribe');
        if (!empty($data)){     //有自动回复
            return $this->reply($data);
        }else {                 //无自动回复
            return '欢迎关注本公众号';
        }
    }

   /**
    * Notes：取消关注
    * Author：张恩来<1059008079@qq.com>
    */
    public function unsubscribe()
    {
        User::where(['openid'=>$this->message['FromUserName']])->update(['subscribe_state'=>1]);
    }

    /**
     * Notes：上报地理位置事件
     * Author：张恩来<1059008079@qq.com>
     * 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，或在进入会话后每5秒上报一次地理位置，公众号可以在公众平台网站中修改以上设置。上报地理位置时，微信会将上报地理位置事件推送到开发者填写的URL。
     */
    public function location()
    {
        //自动回复
        $data = $this->needAutoReply('location');
        if (!empty($data)){     //有自动回复
            return $this->reply($data);
        }else {                 //无自动回复
            return '我们已收到您发送的位置信息';
        }
    }
  
   /**
    * Notes：扫码事件
    * Author：张恩来<1059008079@qq.com>
    */
    public function scan()
    {
        //自动回复(扫码也是关注事件）
        $data = $this->needAutoReply('subscribe');
        if (!empty($data)){     //有自动回复
            return $this->reply($data);
        }else {                 //无自动回复
            return '我们已收到您的扫码信息';
        }
    }

    /**
     * Notes：点击自定义菜单事件
     * Author：张恩来<1059008079@qq.com>
     */
    public function click()
    {
        //自动回复
        $data = $this->needAutoReply('click');
        if (!empty($data)){     //有自动回复
            return $this->reply($data);
        }else {                 //无自动回复
            return '我们已收到您的点击信息';
        }
    }
    /**
     * Notes：获取是否有设置自动回复
     * Author：张恩来<1059008079@qq.com>
     */
    public function needAutoReply($msgType)
    {
        return WeReply::where(['msg_type' => $msgType, 'status' => 1],'expires_date','>',time())->order('id desc')->find();
    }
    /**
     * Notes：回复消息
     * Author：张恩来<1059008079@qq.com>
     */
    public function reply($data)
    {
        switch ($data['type']) {
            case 'text':
                return $data['content'];
                break;
            case 'image':
                if (is_numeric($data['content'])) {
                    $material = WeMaterial::where(['id' => $data['content'], 'type' => 'image'])->find();
                    if ($material) {
                        return new Image($material['media_id']);
                    }
                }
                break;
            case 'voice':
                if (is_numeric($data['content'])) {
                    $material = WeMaterial::where(['id' => $data['content'], 'type' => 'voice'])->find();
                    if ($material) {
                        return new Voice($material['media_id']);
                    }
                }
                break;
            case 'video':
                if (is_numeric($data['content'])) {
                    $material = WeMaterial::where(['id' => $data['content'], 'type' => 'video'])->find();
                    if ($material) {
                        $material_content = json_decode($material['content'], true);
                        return new Video($material['media_id'],[
                            'title' => $material_content['title'],
                            'description' => $material_content['description'],
                        ]);
                    }
                }
                break;
            case 'article':
                //图文(文章)素材
                if (is_numeric($data['content'])) {
                    $material = WeMaterial::where(['id' => $data['content'],'type'=>'article'])->find();
                    if ($material) {
                        $news_material = $this->app->material->get($material['media_id']);
                        return new News([
                            'title' => $news_material['news_item'][0]['title'],
                            'description' => $news_material['news_item'][0]['digest'],
                            'url' => $news_material['news_item'][0]['url'],
                            'image' => $news_material['news_item'][0]['thumb_url'],
                        ]);
                    }
                }
                break;
            case 'news':
                if (is_numeric($data['content'])) {
                    $material = WeMaterial::where(['id' => $data['content'],'type'=>'news'])->find();
                    if ($material) {
                        $material_content = json_decode($material['content'], true);
                        return new News([
                            'title' => $material_content['title'],
                            'description' => $material_content['description'],
                            'url' => $material_content['url'],
                            'image' => request()->domain() . get_file_path($material_content['image']),
                        ]);
                    }
                }
                break;
        }
        return false;
    }
}