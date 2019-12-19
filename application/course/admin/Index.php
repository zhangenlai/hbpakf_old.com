<?php

namespace app\course\admin;

use app\common\builder\ZBuilder;
use app\admin\controller\Admin;
use app\course\model\CourseList;

/**
 * Notes：课程控制器
 * Author：张恩来<1059008079@qq.com>
 */
class Index extends Admin
{
    // 验证失败是否抛出异常
    protected $failException = true;

    /**
     * Notes：课程首页
     * Author：张恩来<1059008079@qq.com>
     */
    public function index()
    {
        $map = $this->getMap();
        $order = $this->getOrder() ? $this->getOrder() : 'id desc';
        $data = CourseList::where($map)->order($order)->paginate();

        $btnResource = [
//            'class' => 'btn btn-primary',
            'icon'  => 'fa fa-fw fa-folder-open',
            'title' => '课程资源',
            'target' => '_blank',
            'href'  => url('resource/index',['pid'=>'__id__'])
        ];
        $addFields = [
            ['text','title','标题'],
            ['select','cid','课程分类','',parse_attr(module_config('course.category'))],
            ['image','image','课程图片']
        ];
        $editFields = [
            ['hidden','id'],
            ['text','title','标题'],
            ['select','cid','课程分类','',parse_attr(module_config('course.category'))],
            ['image','image','课程图片']
        ];

        return ZBuilder::make('table')
            ->setTableName('course_list')
            ->addFilter('cid',parse_attr(module_config('course.category')))
            ->setSearch(['id'=>'ID','title'=>'课程名称'])
            ->hideCheckBox()
            ->addColumns([
                ['id','ID'],
                ['title','课程名称','text.edit'],
                ['cid','课程分类',parse_attr(module_config('course.category'))],
                ['image','课程图片','picture'],
                ['status','状态','switch'],
                ['create_time','添加时间'],
                ['right_button','操作']
            ])
            ->autoAdd($addFields,'CourseList','CourseList',true)
            ->autoEdit($editFields,'CourseList','CourseList',true)
            ->addRightButton('delete')
            ->addRightButton('custom',$btnResource)
            ->setRowList($data)
            ->fetch();
    }
}