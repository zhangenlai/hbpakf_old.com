<?php
namespace app\poetry\admin;

use app\common\builder\ZBuilder;
use app\poetry\model\PluginPoetry;
use app\admin\controller\Admin;
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
        $data = PluginPoetry::where($map,['status'=>1])->order($order)->paginate();
        
        //预览按钮
        $btn_look = [
            'title' => '查看',
            'icon' => 'fa fa-fw fa-eye',
            'class' => 'btn btn-xs btn-default',
            'href' => url('eye',['id'=>'__id__']),
            'target' => '_blank'
        ];
        //顶部采集按钮
        $btn_gather = [
            'title' => '采集',
            'icon' => 'fa fa-fw fa-hand-rock-o',
            'class' => 'btn btn-action btn-primary btn-default',
            'href' => url('gather'),
            'target' => '_blank'
        ];

        return ZBuilder::make('table')
            ->setTableName('plugin_poetry')
            ->addFilter('dynasty,author')
            ->setSearch(['title'=>'标题','dynasty'=>'朝代','author'=>'作者','recomment'=>'推荐内容'])
            ->addColumns([
//                ['id','ID'],
//                ['title','标题'],
//                ['dynasty','朝代'],
//                ['author','作者'],
//                ['recomment','推荐内容'],
//                ['create_time','添加时间'],
//                ['right_button','操作']
            ])
            ->addTopButtons('add,delete')
            ->addTopButton('custom',$btn_gather,[])
            ->addRightButton('delete')
            ->addRightButton('custom',$btn_look,[])
//            ->setRowList($data)
            ->fetch();
    }

    /**
     * Notes：新增
     * Author：张恩来<1059008079@qq.com>
     */
    public function add()
    {
        if($this->request->isPost()){
            $data = $this->request->post();
            dump($data);die;
        }

        return ZBuilder::make('form')
            ->addFormItems([
                ['file','file','文件']
            ])
            ->fetch();
    }

}