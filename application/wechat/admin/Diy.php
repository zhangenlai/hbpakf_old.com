<?php

namespace app\wechat\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\wechat\model\WeDiy as DiyModel;
use util\Tree;

/*
 * 自动回复管理
 */

class Diy extends Admin
{
    // 列表
    public function index()
    {

        // 获取查询条件
        $map = $this->getMap();

        // 数据列表
        $data_list = DiyModel::where($map)->column(true);
		if (empty($map)) {
            $data_list = Tree::config(['title' => 'name'])->toList($data_list);
        }
		 $btnCreate = [
            'class' => 'btn btn-primary',
            'icon'  => 'fa fa-fw fa-plus',
            'title' => '生成自定义菜单',
            'href'  => url('create')
        ];

        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('自定义菜单')// 设置页面标题
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['name', '栏目名称', 'callback', function($value, $data){
                   return isset($data['title_prefix']) ? $data['title_display'] : $value;
               }, '__data__'],
                ['type', '类型',['1'=>'关键词','2'=>'链接']],
                ['orderby', '排序'],
                ['right_button', '操作', 'btn']
            ])
            ->addTopButtons('add,delete')// 批量添加顶部按钮
			->addTopButton('custom',$btnCreate)
            ->addRightButtons('edit,delete')// 批量添加右侧按钮
            ->setRowList($data_list)// 设置表格数据
            ->fetch(); // 渲染页面
    }
	
	function add(){
		// 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
			if (false === DiyModel::create($data)) {
                $this->error('创建失败');
            }
            $this->success('创建成功');
			
		}
		
		 // 显示添加页面
        return ZBuilder::make('form')
            ->addFormItems([
				['text', 'pid', '上级菜单ID', '必填'],
                ['text', 'name', '标题', '必填'],
                ['radio', 'type', '触发类型', '必填' ,['1'=>'关键词','2'=>'链接'],1],
				['text', 'content', '内容', '关键词写触发关键词，链接写跳转链接http://开头'],
				['text', 'orderby', '排序', '必填'],


            ])
			->isAjax(true)
            ->fetch();
		}
		
		function edit($id=null){
            if ($id === 0) $this->error('参数错误');
            // 保存数据
            if ($this->request->isPost()) {
                $data = $this->request->post();
                $diyModel = DiyModel::get($data['id']);
                if (false === $diyModel->save($data)) {
                    $this->error('更新失败');
                }
                $this->success('更新成功');

            }
            $info=DiyModel::get($id);
		     // 显示添加页面
            return ZBuilder::make('form')
            ->addFormItems([
				['hidden','id',$id],
				['text', 'pid', '上级菜单ID', '必填'],
                ['text', 'name', '标题', '必填'],
                ['radio', 'type', '触发类型', '必填' ,['1'=>'关键词','2'=>'链接'],1],
				['text', 'content', '内容', '关键词写触发关键词，链接写跳转链接http://开头'],
				['text', 'orderby', '排序', '必填'],
            ])
                ->setFormData($info)
                ->isAjax(true)
                ->fetch();
		}

		/**
		 * Notes：生成自定义菜单(更新)
		 * Author：张恩来<1059008079@qq.com>
		 */
		function create()
        {
			$pData=DiyModel::where(['pid'=>0])->limit(3)->order('orderby desc')->select();
			$menu = [];
			foreach ($pData as $k => $v){
                $subData = DiyModel::where(['pid'=>$v['id']])->limit(5)->select();
                if (empty($subData[0])){   //没有子菜单
                    if ($v['type'] == 2) {    //链接
                        $menu[$k] = [
                            'type' => 'view',
                            'name' => $v['name'],
                            'url' => $v['content'],
                        ];
                    }else {                 //关键词
                        $menu[$k] = [
                            'type' => 'click',
                            'name' => $v['name'],
                            'key' => $v['content'],
                        ];
                    }
                }else {     //有子菜单
                    $subButton = [];
                    foreach ($subData as $key => $val){
                        if ($val['type'] == 2){   //链接
                            $subButton[$key] = [
                                'type' => 'view',
                                'name' => $val['name'],
                                'url' => $val['content'],
                            ];
                        }else {                 //关键词
                            $subButton[$key] = [
                                'type' => 'click',
                                'name' => $val['name'],
                                'key' => $val['content'],
                            ];
                        }
                    }
                        $menu[$k] = [
                            'name' => $v['name'],
                            'sub_button' => $subButton,
                        ];
                }
            }
            $result = $this->wechatApp->menu->create($menu);
            if($result['errmsg']=="ok")
            $this->success('更新成功');
            else
            $this->success('更新失败');
	    }
			


    
}