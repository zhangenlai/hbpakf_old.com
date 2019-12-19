<?php
namespace app\course\admin;

use app\common\builder\ZBuilder;
use app\admin\controller\Admin;
/**
 * Notes：课程全局配置控制器
 * Author：张恩来<1059008079@qq.com>
 */
class Config extends Admin
{
    public function index()
    {
        
        return $this->moduleConfig();
    }
}