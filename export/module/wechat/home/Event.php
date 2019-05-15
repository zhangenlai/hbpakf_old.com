<?php

namespace app\wechat\home;

use app\index\controller\Home;
use app\wechat\model\WeMaterial;
use app\wechat\model\WeReply;
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
        $data = $this->needAutoReply('subscribe');
        if (!empty($data)){     //有自动回复
            $this->reply($data);
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

    }

    /**
     * Notes：上报地理位置事件
     * Author：张恩来<1059008079@qq.com>
     * 用户同意上报地理位置后，每次进入公众号会话时，都会在进入时上报地理位置，或在进入会话后每5秒上报一次地理位置，公众号可以在公众平台网站中修改以上设置。上报地理位置时，微信会将上报地理位置事件推送到开发者填写的URL。
     */
    public function location()
    {

    }
  
   /**
    * Notes：扫码事件
    * Author：张恩来<1059008079@qq.com>
    */
    public function scan()
    {

    }

    /**
     * Notes：点击自定义菜单事件
     * Author：张恩来<1059008079@qq.com>
     */
    public function click()
    {

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