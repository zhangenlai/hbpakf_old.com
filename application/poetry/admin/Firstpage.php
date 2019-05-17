<?php

namespace app\poetry\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use app\poetry\model\MyMessage;
use app\poetry\model\PacketWechatArea;

class Firstpage extends Admin
{
    /**
     * Notes：我的信息
     * Author：张恩来<1059008079@qq.com>
     */
    public function mymessage()
    {
        $data = MyMessage::find();
        $origin = json_decode($data['origin'],true);
        $data['province'] = $origin[0];
        $data['city'] = $origin[1];
        $province = PacketWechatArea::distinct(true)->field('province')->select();     //省份信息
        foreach ($province as $k => $v){
            $provinceData[$v['province']] = $v['province'];
        }
        $city = [];
        if (!empty($data['city'])){     //如果城市不为空的话
            $cityData = PacketWechatArea::where(['province'=>$data['province']])->field('city')->select();
            foreach ($cityData as $k => $v){
                $city[$v['city']] = $v['city'];
            }
        }
        //修改数据
        if ($this->request->isPost()){
            $data =$this->request->post();

            $data['origin'] = json_encode([$data['province'],$data['city']]);

            if ($myMes = MyMessage::get($data['id'])){
                $myMes->save($data) ? $this->success('修改成功') :$this->error('修改失败');
            }
        }

        return ZBuilder::make('form')
            ->setPageTips('所有文本域换行使用《br》分割')
            ->addFormItems([
                ['hidden','id'],
                ['text:3','name','姓名'],
                ['radio:3','sex','性别','',['男'=>'男','女'=>'女']],
            ])
            ->addLinkage('province','籍贯(省份)','',$provinceData,'',url('getCity'),'city')
            ->addFormItems([
                ['select:3','city','籍贯(城市)','',$city],
                ['text:4','nation','民族'],
                ['date:4','birthday','生日'],
                ['number:4','age','年龄','','','',0],
                ['number:4','height','身高(cm)'],
                ['number:4','tel','电话'],
                ['text:4','email','邮箱'],
                ['text:4','qq','QQ'],
                ['text:4','school','毕业学校'],
                ['text:4','education','学历'],
                ['textarea:4','educationex','教育经历'],
                ['textarea:4','work','工作经历'],
                ['textarea:4','workex','参与项目'],
                ['textarea:4','workexex','额外经历'],
                ['textarea:4','skill','专业技能'],
                ['textarea:4','spec','个人特长'],
            ])
            ->layout(['province'=>3])
            ->setFormData($data)
            ->fetch();
    }

    /**
     * Notes：获取城市信息
     * Author：张恩来<1059008079@qq.com>
     */
    function getCity()
    {
        $data = $this->request->post();
        $data = PacketWechatArea::where(['province'=>$data['province']])->field('city')->select();
        $arr['code'] = '1'; //判断状态
        $arr['msg'] = '请求成功'; //回传信息
        foreach ($data as $k => $v){
            $arr['list'][$k]['key'] = $v['city'];
            $arr['list'][$k]['value'] = $v['city'];
        }
        return json($arr);
    }
}
