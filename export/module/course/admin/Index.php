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

    /**
     * Notes：课程首页
     * Author：张恩来<1059008079@qq.com>
     */
    public function index()
    {
        $map = $this->getMap();
        $order = $this->getOrder() ? $this->getOrder() : 'id desc';
        $data = CourseList::where($map)->order($order)->paginate();

        return ZBuilder::make('table')
            ->setTableName('course_list')
            ->addFilter('cid',parse_attr(module_config('course.category')))
            ->setSearch(['id'=>'ID','title'=>'课程名称'])
            ->hideCheckBox()
            ->addColumns([
                ['id','ID'],
                ['title','课程名称'],
                ['cid','课程分类',parse_attr(module_config('course.category'))],
                ['create_time','添加时间'],
                ['right_button','操作']
            ])
            ->addTopButtons('add',[],true)
            ->addRightButton('delete')
            ->setRowList($data)
            ->fetch();
    }

    /**
     * Notes：添加
     * Author：张恩来<1059008079@qq.com>
     */
    public function add()
    {
        return ZBuilder::make('form')
            ->addFormItems([
                ['text:4','title','课程名称'],
                ['select:4','cid','课程分类','',parse_attr(module_config('course.category'))],
            ])
            ->fetch();
    }
}