<?php

namespace app\wechat\home;

use EasyWeChat\Kernel\Messages\Text;    // 文本消息
use EasyWeChat\Kernel\Messages\Image;
use EasyWeChat\Kernel\Messages\Video;
use EasyWeChat\Kernel\Messages\Voice;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messagess\Media;
use EasyWeChat\Kernel\Messages\Article;
use app\wechat\model\WeReply;
use app\wechat\model\WeMaterial;

/*
 * 处理接收到的除事件外的消息
 * 方法名全部小写
 */

class Message
{
    protected $message;
    protected $app;

    public function __construct($message, $app)
    {
        $this->message = $message;
        $this->app = $app;
    }

    // 收到文字消息的处理方法
    public function text()
    {
        $data = WeReply::where(['keyword' => $this->message['Content'], 'msg_type' => 'text', 'status' => 1],'expires_date','>',time())->order('id desc')->find();
        if (!empty($data) && $data['mode']==1){     //精确搜索
                return $this->reply($data);
        }else {
            $data = WeReply::where('keyword','like','%'.$this->message['Content'].'%',['msg_type'=>'text','status'=>1])->order('id desc')->find();
            if (!empty($data) && $data['mode'] == 0){     //模糊搜索
                return $this->reply($data);
            }
        }

    }

    // 收到图片消息的处理方法
    public function image()
    {
        return '我们已收到您的图片消息';
    }

    // 收到语音消息的处理方法
    public function voice()
    {
        return '我们已收到您的语音消息';
    }

    // 收到视频消息的处理方法
    public function video()
    {
        return '我们已收到您的视频消息';
    }

    // 收到地理位置消息的处理方法
    public function location()
    {
        return '我们已收到您的地理位置消息';
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
                            new NewsItem([
                            'title' => $news_material['news_item'][0]['title'],
                            'description' => $news_material['news_item'][0]['digest'],
                            'url' => $news_material['news_item'][0]['url'],
                            'image' => $news_material['news_item'][0]['thumb_url'],
                            ]),
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
                            new NewsItem([
                            'title' => $material_content['title'],
                            'description' => $material_content['description'],
                            'url' => $material_content['url'],
                            'image' => request()->domain() . get_file_path($material_content['image']),
                            ]),
                        ]);
                    }
                }
                break;
        }
        return false;
    }
}