<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------
namespace app\course\home;

use app\index\controller\Home;

class Index extends Home
{
    public function index()
    {
        $config = parse_attr(module_config('course.type'));
        dump($config);die;
    }

}
