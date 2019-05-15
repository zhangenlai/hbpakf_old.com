<?php
namespace app\poetry\admin;

use app\common\builder\ZBuilder;
use app\poetry\model\PluginPoetry;
use app\admin\controller\Admin;
use QL\QueryList;
/**
 * Notes：诗词控制器
 * Author：张恩来<1059008079@qq.com>
 */
class Index extends Admin
{
    /**
     * Notes：诗词首页
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
            ->addTopButton('delete')
            ->addTopButton('custom',$btn_gather,[])
            ->addRightButton('delete')
            ->addRightButton('custom',$btn_look,[])
//            ->setRowList($data)
            ->fetch();
    }

    /**
     * Notes：查看详情
     * Author：张恩来<1059008079@qq.com>
     */
    public function eye($id=null)
    {
        if (empty($id))$this->error('参数错误!',null,'_parent_reload');
        $data = PluginPoetry::get($id);
        $data['content'] = str_replace('#','',$data['content']);       //分割内容(多行)
        $this->assign('data',$data);
        return ZBuilder::make('form')
            ->addGroup(
                [
                    '诗词' =>[
                        ['static:6','title','标题'],
                        ['static:3','dynasty','朝代'],
                        ['static:3','author','作者'],
                        ['static:6','content','内容'],
                    ],
                    '译文' => [
                        ['static','translate','译文']
                    ]
                ]
            )
            ->setFormData($data)
            ->fetch();
    }

    /**
     * Notes：采集
     * Author：张恩来<1059008079@qq.com>
     */
    public function gather()
    {
        $data = PluginPoetry::all();
        foreach ($data as $k => $v){
            $url = $v['link'];
            // 元数据采集规则
            $rules = [
                // 译文
                'translate' => ['.contyishang', 'text'],
            ];
            $range = '.left';
            $rt = QueryList::get($url)->rules($rules)->range($range)->queryData();

            if (!empty($rt)) {
                foreach ($rt as $k => $v) {
                    if (!empty($v['title'] && !empty($v['link']) && !empty($v['dynasty']) && !empty($v['author'] && !empty($v['content']) && !empty($v['tags'])))) {
                        $insData = [
                            'title' => $v['title'],
                            'link' => $v['link'],
                            'dynasty' => $v['dynasty'],
                            'author' => $v['author'],
                            'content' => $v['content'],
                            'tags' => str_replace('\n', '', json_encode(explode('，', $v['tags']), JSON_UNESCAPED_UNICODE)),
                        ];
                        if (!PluginPoetry::get($insData)) {
                            PluginPoetry::create($insData);
                        }
                    }
                }
            }
            $nextPage = QueryList::get($url)->rules(['nextPage' => ['.pagesright>.amore', 'href']])->queryData();
            $nextPage = $nextPage[0]['nextPage'];
        }
    }
}