<?php

namespace app\course\admin;

use app\common\builder\ZBuilder;
use app\admin\controller\Admin;
use app\course\model\CourseResource as CResource;

/**
 * Notes：课程资源
 * Author：张恩来<1059008079@qq.com>
 */
class Resource extends Admin
{
    // 验证失败是否抛出异常
    protected $failException = true;

    /**
     * index
     */
    public function index($pid=null)
    {
        if (empty($pid))$this->error('PID参数错误');
        $map = $this->getMap();
        $order = $this->getOrder() ? $this->getOrder() : 'id desc';
        $data = CResource::where($map)->where('pid',$pid)->order($order)->paginate();

        // 定义新增页面的字段
        $addFields = [
            ['hidden','pid',$pid],
            ['text', 'name', '资源名称'],
            ['select', 'type', '资源类型', '',parse_attr(module_config('course.type'))],
            ['file', 'file', '文件', ''],
        ];
        // 定义编辑页面的字段
        $editFields = [
            ['hidden','id'],
            ['text', 'name', '资源名称'],
            ['select', 'type', '资源类型', '',parse_attr(module_config('course.type'))],
            ['file', 'file', '文件', ''],
        ];

        return ZBuilder::make('table')
            ->setTableName('CourseResource')
            ->addFilter('type',parse_attr(module_config('course.type')))
            ->hideCheckBox()
            ->addColumns([
                ['id','ID'],
                ['name','资源名称','text.edit'],
                ['type','资源类型',parse_attr(module_config('course.type'))],
                ['file','文件','files'],
                ['status','上架状态','switch'],
                ['create_time','创建时间'],
                ['right_button','操作']
            ])
            ->setRowList($data)
            ->autoAdd($addFields,'CourseResource','CourseResource',true)
            ->autoEdit($editFields,'CourseResource','CourseResource',true)
            ->addRightButton('delete',true)
            ->fetch();
    }
}