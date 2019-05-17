<?php

namespace app\wechat\home;

use EasyWeChat\Kernel\Messages\Text;    // 文本消息
use app\wechat\model\WeReply;

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
            $this->reply($data);
        }else {
            $data = WeReply::where('keyword','like','%'.$this->message['Content'].'%',['msg_type'=>'text','status'=>1])->order('id desc')->find();
            if (!empty($data) && $data['mode'] == 2){     //模糊搜索
                $this->reply($data);
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
                    $material = Db::name('we_material')->field(true)->where(['id' => $data['content'], 'type' => 'image'])->find();
                    if ($material) {
                        return new Image(['media_id' => $material['media_id']]);
                    }
                }
                break;
            case 'voice':
                if (is_numeric($data['content'])) {
                    $material = Db::name('we_material')->field(true)->where(['id' => $data['content'], 'type' => 'voice'])->find();
                    if ($material) {
                        return new Voice(['media_id' => $material['media_id']]);
                    }
                }
                break;
            case 'video':
                if (is_numeric($data['content'])) {
                    $material = Db::name('we_material')->field(true)->where(['id' => $data['content'], 'type' => 'video'])->find();
                    if ($material) {
                        $material_content = json_decode($material['content'], true);
                        return new Video([
                            'title' => $material_content['title'],
                            'media_id' => $material['media_id'],
                            'description' => $material_content['description'],
                        ]);
                    }
                }
                break;
            case 'article':
                if (is_numeric($data['content'])) {
                    $material = Db::name('we_material')->field(true)->where(['id' => $data['content']])->find();
                    if ($material) {
                        $news_material = $this->app->material->get($material['media_id']);
                        $news = new News();
                        $news->title = $news_material['news_item'][0]['title'];
                        $news->description = $news_material['news_item'][0]['digest'];
                        $news->url = $news_material['news_item'][0]['url'];
                        $news->image = $news_material['news_item'][0]['thumb_url'];
                        return $news;
                    }
                }
                break;
            case 'news':
                if (is_numeric($data['content'])) {
                    $material = Db::name('we_material')->field(true)->where(['id' => $data['content']])->find();
                    if ($material) {
                        $material_content = json_decode($material['content'], true);
                        $news = new News();
                        $news->title = $material_content['title'];
                        $news->description = $material_content['description'];
                        $news->url = $material_content['url'];
                        $news->image = request()->domain() . get_file_path($material_content['image']);
                        return $news;
                    }
                }
                break;
        }
        return false;
    }
}