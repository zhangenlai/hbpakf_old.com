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

/**
 * 单子记录
 * @package app\user\admin
 */
class Dan extends Admin
{

    /**
     * 首页
     */
    public function index()
    {
        $map = $this->getMap();
        $order = $this->getOrder() ? $this->getOrder : 'id desc';
        $data = db::table('dp_admin_dan')->where($map)->order($order)->paginate();

        return ZBuilder::make('table')
            ->hideCheckBox()
            ->addFilter('status',['未结算','已结算'])
            ->setSearch(['id'=>'ID','name'=>'名称','status'=>'结算状态'])
            ->setTableName('admin_dan')
            ->addColumns([
                ['id','ID'],
                ['name','名称','text.edit'],
                ['price','原价','text.edit'],
                ['money','实际','text.edit'],
                ['status','结算状态','switch',['未结算','已结算']],
                ['username','用户名称'],
                ['userfrom','用户来源',['QQ','微信']],
                ['userlx','用户联系方式'],
                ['starttime','开始时间'],
                ['endtime','结束时间'],
                ['book','备注','text.edit'],
                ['right_button','操作','btn']
            ])
            ->addTopButton('add',[],true) // 批量添加顶部按钮
            ->addRightButton('delete') // 批量添加右侧按钮
            ->setRowList($data)
            ->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()){
            $data = $this->request->post();
            if (empty($data['money'])) {
                $data['money'] = $data['price'] * 0.8;
            }
            $res = db::table('dp_admin_dan')->insert($data);
            return $res ? $this->success('添加成功',null,'_parent_reload') : $this->error('添加失败',null,'_parent_reload');
        }
        return ZBuilder::make('form')
            ->addFormItems([
                ['text:4','name','名称'],
                ['text:4','price','原价'],
                ['text:4','money','实际（默认百分之八十）'],
                ['text:4','username','用户名称'],
                ['select:4','userfrom','用户来源','',['QQ','微信'],0],
                ['text:4','userlx','用户联系方式'],
                ['date:6','starttime','开始时间'],
                ['date:6','endtime','结束时间'],
                ['select:6','status','结算状态','',['未结算','已结算'],0],
                ['textarea:8','book','备注']
            ])
            ->fetch();
    }
}