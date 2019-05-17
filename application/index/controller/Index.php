<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\index\controller;
use app\poetry\model\MyMessage;

/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Home
{

    /**
     * Notes：首页
     * Author：张恩来<1059008079@qq.com>
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * Notes：我的信息
     * Author：张恩来<1059008079@qq.com>
     */
    public function myInformation()
    {
        return $this->fetch('myInformation');
    }
}
