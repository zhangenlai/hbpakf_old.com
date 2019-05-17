<?php

namespace app\wechat\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\wechat\model\WeReply as WeReplyModel;
use app\wechat\model\WeMaterial as WeMaterialModel;
use app\wechat\validate\WeMaterial as WeMaterialVal;

/*
 * 自动回复管理
 */

class Reply extends Admin
{
    // 列表
    public function index()
    {
        cookie('__forward__', $_SERVER['REQUEST_URI']);
        $btn_access = [
            'title' => '新增',
            'icon'  => 'fa fa-plus-circle',
            'class' => 'btn btn-primary',
            'href'  => url('add')
        ];
        // 获取查询条件
        $map = $this->getMap();

        // 数据列表
        $data_list = WeReplyModel::where($map)->order('id desc')->paginate();

        // 分页数据
        $page = $data_list->render();
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('自动回复列表')// 设置页面标题
            ->setTableName('WeReply')// 设置数据表名
            ->setSearch(['id' => 'ID', 'keyword' => '关键词', 'content' => '回复内容'])// 设置搜索参数
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['msg_type', '触发方式','','',['text' => '回复关键词', 'image' => '回复图片', 'voice' => '回复语音', 'video' => '回复视频', 'location' => '回复位置', 'subscribe' => '关注事件', 'location_event' => '上报位置事件', 'click' => '点击自定义菜单事件']],
                ['keyword', '触发关键词'],
                ['type', '回复类型','','',['text' => '文字内容', 'image' => '图片素材', 'voice' => '声音素材', 'video' => '视频素材', 'thumb' => '缩略图素材', 'article' => '图文(文章)素材', 'news' => '图文(外链)素材']],
                ['content', '回复内容', 'callback', function($value, $data){
                    if (is_numeric($data['content'])) {
                          return '<a href="' . url('wechat/material/index', 'search_field=id&keyword=' . $data['content']) . '"
                                  rel="noreferrer"
                                  target="_blank">素材ID：' . $data['content'] . '</a>';
                      } else {
                          return string_cut($data['content'], 15);
                      }
                }, '__data__'],
                ['expires_date', '有效期', 'callback', function($value, $data){
                           if ($data['expires_date'] < time()) {
                                return '<span class="font-w600 text-danger" style="text-decoration: line-through; ">' . date('Y-m-d', $data['expires_date']) . '</span>';
                            } else {
                                return '<span class="font-w600 text-success">' . date('Y-m-d', $data['expires_date']) . '</span>';
                            }
                }, '__data__'],
                ['create_time', '创建日期'],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn']
            ])
            ->addTopButton('custom', $btn_access,[], true)
            ->addTopButton('delete')// 批量添加顶部按钮
            ->addRightButton('edit',[],true)// 批量添加右侧按钮
            ->addRightButton('delete')// 批量添加右侧按钮
            ->setRowList($data_list)// 设置表格数据
            ->setPages($page)// 设置分页数据
            ->fetch(); // 渲染页面
    }

    // 添加自动回复
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
            $result = $this->validate($data,'WeReply');
            if ($result === true && $data['type'] != 'text') {
                $data['content'] = $data['contentOther'];
            }else if ($result === true && $data['type'] == 'text'){
                $data['content'] = $data['contentText'];
            }
            // 验证失败 输出错误信息
            if (true !== $result) return $this->error($result);

            if ($user = WeReplyModel::create($data)) {
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }
        }

        $default_expires_date = date(   'Y-m-d', strtotime('+30 day', time()));    // 默认有效期为30天之后
        return ZBuilder::make('form')
            ->setPageTitle('添加关键词')// 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['select', 'msg_type', '触发方式', '用户以什么方式触发自动回复', ['text' => '回复关键词', 'image' => '回复图片', 'voice' => '回复语音', 'video' => '回复视频', 'location' => '回复位置', 'subscribe' => '关注事件', 'location_event' => '上报位置事件', 'click' => '点击自定义菜单事件'], 'text'],
                ['text', 'keyword', '关键词'],
                ['radio', 'mode', '匹配模式', '', ['模糊搜索', '完整匹配'], 1],
            ])
            ->addLinkage('type','回复类型','', ['text' => '文字内容', 'image' => '图片素材', 'voice' => '声音素材', 'video' => '视频素材', 'article' => '图文(文章)素材', 'news' => '图文(外链)素材'], 'text',url('getMaterial'), 'contentOther')
            ->addSelect('contentOther','回复素材ID')
            ->addFormItems([
                ['textarea', 'contentText', '回复内容'],
                ['date', 'expires_date', '有效期', '默认有效期为30天之后。小于或等于当前日期 <code>' . date('Y-m-d') . '</code> 则表示过期', $default_expires_date, 'yyyy-mm-dd'],
                ['radio', 'status', '状态', '', ['禁用', '启用'], 1],
            ])
            ->setTrigger('msg_type', 'text', 'keyword,mode,contentText')
            ->setTrigger('type', 'text', 'contentText')
            ->setTrigger('type', 'image,voice,video,article,news', 'contentOther')
            ->fetch();
    }

    /**
     * Notes：获取回复类型对应素材
     * Author：张恩来<1059008079@qq.com>
     */
    function getMaterial()
    {
        $data = $this->request->post();
        $data = WeMaterialModel::where(['type'=>$data['type']])->select();
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($data as $k => $v){
            $arr['list'][$k] = [
                'key' => $v['id'],
                'value' => $v['name'],
            ];
        }
        return json($arr);
    }
    // 编辑
    public function edit($id = null)
    {
        if ($id === null) return $this->error('缺少参数');

        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证
            $result = $this->validate($data, 'WeReply');
                if ($result === true && $data['type'] != 'text') {
                    $data['content'] = $data['contentOther'];
                }else if ($result === true && $data['type'] == 'text'){
                    $data['content'] = $data['contentText'];
                }
            // 验证失败 输出错误信息
            if (true !== $result) return $this->error($result);
            $weReply = WeReplyModel::get($data['id']);
            if ($weReply->save($data)) {
                return $this->success('编辑成功');
            } else {
                return $this->error('编辑失败');
            }
        }

        // 获取数据
        $we_reply_info = WeReplyModel::get($id);
        if (!$we_reply_info) {
            return $this->error('内容不存在');
        }
        $material = [];
        if ($we_reply_info['type'] == 'text'){
            $we_reply_info['contentText'] = $we_reply_info['content'];
        }else {
            //给下拉框赋默认值
            $we_reply_info['contentOther'] = $we_reply_info['content'];
            $material = [
                $we_reply_info['content'] => WeMaterialModel::get($we_reply_info['content'])->value('name'),
            ];
        }

        $default_expires_date = date('Y-m-d', strtotime('+30 day', time()));    // 默认有效期为30天之后

        return ZBuilder::make('form')
            ->setPageTitle('编辑关键词')// 设置页面标题
            ->addFormItems([ // 批量添加表单项
                ['hidden', 'id'],
                ['select', 'msg_type', '触发方式', '用户以什么方式触发自动回复', ['text' => '回复关键词', 'image' => '回复图片', 'voice' => '回复语音', 'video' => '回复视频', 'location' => '回复位置', 'subscribe' => '关注事件', 'location_event' => '上报位置事件', 'click' => '点击自定义菜单事件'], 'text'],
                ['text', 'keyword', '关键词'],
                ['radio', 'mode', '匹配模式', '', ['模糊搜索', '完整匹配'], 1],
            ])
            ->addLinkage('type','回复类型','', ['text' => '文字内容', 'image' => '图片素材', 'voice' => '声音素材', 'video' => '视频素材', 'article' => '图文(文章)素材', 'news' => '图文(外链)素材'], 'text',url('getMaterial'), 'contentOther')
            ->addSelect('contentOther','回复素材ID','',$material)
            ->addFormItems([
                ['textarea', 'contentText', '回复内容'],
                ['date', 'expires_date', '有效期', '默认有效期为30天之后。小于或等于当前日期 <code>' . date('Y-m-d') . '</code> 则表示过期', $default_expires_date, 'yyyy-mm-dd'],
                ['radio', 'status', '状态', '', ['禁用', '启用'], 1],
            ])
            ->setTrigger('msg_type', 'text', 'keyword,mode,contentText',false)
            ->setTrigger('type', 'text', 'contentText',false)
            ->setTrigger('type', 'image,voice,video,article,news', 'contentOther',false)
            ->setFormData($we_reply_info)// 设置表单数据
            ->fetch();
    }
}