<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace plugins\HelloWorld\controller;

use think\Session;
use app\common\builder\ZBuilder;
use app\common\controller\Common;
use plugins\HelloWorld\model\HelloWorld;
use plugins\HelloWorld\validate\HelloWorld as HelloWorldValidate;

/**
 * 插件后台管理控制器
 * @package plugins\HelloWorld\controller
 */
class Admin extends Common
{
    /**
     * 插件管理页
     * @author 张恩来 <1059008079@qq.com>
     * @return mixed
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function index()
    {
        // 查询条件
        $map = $this->getMap();

        $data_list = HelloWorld::where($map)->order('id desc')->paginate();
        // 分页数据
        $page = $data_list->render();

        // 自定义按钮
        $btnOne = [
            'title'  => '获取诗词信息(30条)',
            'icon'   => 'fa fa-file',
            'href'   => plugin_url('HelloWorld/Admin/getPoetry'),
        ];
        $btnBack['title'] = '返回插件列表';
        $btnBack['icon'] = 'fa fa-reply';
        $btnBack['href'] = url('plugin/index');
        $btnBack['class'] = 'btn btn-warning';

        // 用TableBuilder渲染模板
        return ZBuilder::make('table')
            ->setPageTitle('数据列表')
            ->setSearch(['id' => 'ID', 'said' => '名言', 'name' => '出处'])
            ->addColumn('id', 'ID')
            ->addColumns([
                ['said', '名言'],
                ['name', '出处'],
                ['status', '状态', 'switch'],
                ['right_button', '操作', 'btn']
            ])
            ->addTopButton('custom', $btnBack)
            ->addTopButton('add', ['plugin_name' => 'HelloWorld'],[],true)
            ->addTopButtons('delete')
            ->addTopButton('custom', $btnOne)
            ->addRightButton('edit', ['plugin_name' => 'HelloWorld',],[])
            ->addRightButtons('delete')
            ->setTableName('plugin_poetry')
            ->setRowList($data_list)
            ->setPages($page)
            ->fetch();
    }

    /**
     * 新增
     * @author 张恩来 <1059008079@qq.com>
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 验证数据
            $validate = new HelloWorldValidate();
            if(!$validate->check($data)){
                // 验证失败 输出错误信息
                $this->error($validate->getError());
            }

            // 插入数据
            if (HelloWorld::create($data)) {
                $this->success('新增成功', null,'_parent_reload');
            } else {
                $this->error('新增失败',null,'_parent_reload');
            }
        }

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->addFormItems([
                ['text', 'name', '出处'],
                ['text', 'name', '出处'],
            ])
            ->fetch();
    }

    /**
     * 编辑
     * @author 张恩来 <1059008079@qq.com>
     */
    public function edit()
    {
        if ($this->request->isPost()) {
            $data = $this->request->post();

            // 使用自定义的验证器验证数据
            $validate = new HelloWorldValidate();
            if (!$validate->check($data)) {
                // 验证失败 输出错误信息
                $this->error($validate->getError());
            }

            // 更新数据
            if (HelloWorld::update($data)) {
                $this->success('编辑成功', cookie('__forward__'));
            } else {
                $this->error('编辑失败');
            }
        }

        $id = input('param.id');

        // 获取数据
        $info = HelloWorld::get($id);

        // 使用ZBuilder快速创建表单
        return ZBuilder::make('form')
            ->setPageTitle('编辑')
            ->addFormItem('hidden', 'id')
            ->addFormItem('text', 'name', '出处')
            ->addFormItem('text', 'said', '名言')
            ->setFormData($info)
            ->fetch();
    }

    /**
     * 自定义页面
     * @author 张恩来 <1059008079@qq.com>
     * @return mixed
     */
    public function testPage()
    {
        // 1.使用默认的方法渲染模板，必须指定完整的模板文件名（包括模板后缀）
//        return $this->fetch(config('plugin_path'). 'HelloWorld/view/index.html');

        // 2.使用已封装好的快捷方法，该方法只用于加载插件模板
        // 如果不指定模板名称，则自动加载插件view目录下与当前方法名一致的模板
        return $this->pluginView('','tpl');
//         return $this->pluginView('index'); // 指定模板名称
//         return $this->pluginView('', 'tpl'); // 指定模板后缀
    }
}
