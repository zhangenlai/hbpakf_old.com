<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\user\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\user\model\User as UserModel;
use app\user\model\Role as RoleModel;
use app\admin\model\Module as ModuleModel;
use app\admin\model\Access as AccessModel;
use util\Tree;
use think\Db;
use think\facade\Hook;

class Ze extends Admin
{
    /**
     * 狗泽加的，无聊写的
     */
    public function zl_index(){
        $data_list = db::table('dp_zl')->order('zz_zl','desc')->select();

        $fields = [
            ['text', 'name', '用户名', '必填，可由英文字母、数字组成'],
            ['number', 'zhanli', '上月份战力', '必填，数字组成'],
            ['number', 'new_zhanli', '当前战力', '必填，数字组成'],
        ];

        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setHeight('auto')
            ->setPageTitle('战力增幅') // 页面标题
            ->setTableName('zl') // 设置表名
            ->setSearch(['name' => '角色名称']) // 设置搜索参数
            ->addColumns([
                ['name', '名字'],
                ['zhanli', '上月份战力','text.edit'],
                ['new_zhanli', '当前战力','text.edit'],
                ['zz', '战力增幅','callback',function($data){
                    $data['zz_zl'] = $data['new_zhanli']-$data['zhanli'];
                    $res = Db::table('dp_zl')->where('id', $data['id'])->update(['zz_zl' => $data['zz_zl']]);
                    return $data['zz_zl'];
                },'__data__'],
                ['right_button','操作','btn']

            ])
            ->autoAdd($fields)
            ->addRightButton('delete')
            ->setRowList($data_list) // 设置表格数据
            ->fetch(); // 渲染模板
    }

    public function add(){
        if ($this->request->isPost()){
            $data = $this->request->post();
            $data['zz_zl'] = $data['new_zhanli']-$data['zhanli'];
            $res = db::table('dp_zl')->insert($data);
            return $res ? $this->success('添加成功',null,'_parent_reload') : $this->error('添加失败',null,'_parent_reload');
        }
        return ZBuilder::make('form')
            ->addFormItems([
                ['text','name','名称'],
                ['number','zhanli','上月战力'],
                ['number','new_zhanli','当前战力'],
            ])
            ->fetch();
    }
}