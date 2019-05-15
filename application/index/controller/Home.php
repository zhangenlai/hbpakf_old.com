<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\common\controller\Common;

/**
 * 前台公共控制器
 * @package app\index\controller
 */
class Home extends Common
{
    /**
     * 初始化方法
     * @author 蔡伟明 <314013107@qq.com>
     */
    protected function initialize()
    {
        // 系统开关
        if (!config('web_site_status')) {
            $this->error('站点已经关闭，请稍后访问~');
        }
    }

    //创建日志
    public function createFolder($path) {
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
    }
    //日志
    public function logsave($content)
    {
        // global $host_url;
        if (!file_exists(LOG_PATH . '/log')) {
            $this->createFolder(LOG_PATH . '/log');
        }

        if (!file_exists(LOG_PATH . '/log/' . date('Y-m-d') . '.txt')) {
            $f = fopen(LOG_PATH . '/log/' . date('Y-m-d') . '.txt', "w+");
            chmod(LOG_PATH . '/log/' . date('Y-m-d') . '.txt', 0755);
            fclose($f);
        }

        if (!empty($content)) {
            $content = "\r\n" . date('Y-m-d H:i:s') . "：" . $content;
            file_put_contents(LOG_PATH . '/log/' . date('Y-m-d') . '.txt', $content, FILE_APPEND);
        }
    }
}
