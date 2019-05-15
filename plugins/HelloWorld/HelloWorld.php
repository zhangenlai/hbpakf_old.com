<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace plugins\HelloWorld;

use app\common\controller\Plugin;

/**
 * 演示插件
 * @package plugin\HelloWorld
 * @author 张恩来 <1059008079@qq.com>
 */
class HelloWorld extends Plugin
{
    /**
     * @var array 插件信息
     */
    public $info = [
        // 插件名[必填]
        'name'        => 'HelloWorld',
        // 插件标题[必填]
        'title'       => '推荐诗词',
        // 插件唯一标识[必填],格式：插件名.开发者标识.plugin
        'identifier'  => 'helloworld.ming.plugin',
        // 插件图标[选填]
        'icon'        => 'fa fa-fw fa-globe',
        // 插件描述[选填]
        'description' => '会在每个页面生成一个Tip，展示推荐诗词',
        // 插件作者[必填]
        'author'      => '张恩来',
        // 作者主页[选填]
        'author_url'  => 'http://www.hbpakf.com',
        // 插件版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
        'version'     => '1.0.0',
        // 是否有后台管理功能    (1代表有:controller/Admin)
        'admin'       => '0',
    ];

    /**
     * @var array 管理界面字段信息
     */
    public $admin = [];

    /**
     * @var array 新增或编辑的字段
     */
    public $fields = [];

    /**
     * @var string 原数据库表前缀
     * 用于在导入插件sql时，将原有的表前缀转换成系统的表前缀
     * 一般插件自带sql文件时才需要配置
     */
    public $database_prefix = 'dolphin_';

    /**
     * @var array 插件钩子
     */
    public $hooks = [
        // 钩子名称 => 钩子说明
        // 如果是系统钩子，则钩子说明不用填写
        'page_tips',
        'my_hook'=>'每个页面Tips',
    ];

    /**
     * 我的钩子
     */
    public function myHook()
    {
        echo '<div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p><span id="jinrishici-sentence">正在加载今日诗词....</span>
<script src="https://sdk.jinrishici.com/v2/browser/jinrishici.js" charset="utf-8"></script></p>
        </div>';
    }
    /**
     * 安装方法必须实现
     * 一般只需返回true即可
     * 如果安装前有需要实现一些业务，可在此方法实现
     * @author 张恩来 <1059008079@qq.com>
     * @return bool
     */
    public function install(){
        return true;
    }

    /**
     * 卸载方法必须实现
     * 一般只需返回true即可
     * 如果安装前有需要实现一些业务，可在此方法实现
     * @author 张恩来 <1059008079@qq.com>
     * @return bool
     */
    public function uninstall(){
        return true;
    }
}
