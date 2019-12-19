<?php
namespace app\wechatsma\home;

use app\index\controller\Home;
use EasyWeChat\Factory;
use think\Db;
use think\Session;
use app\course\model\CourseList;
use app\course\model\CourseResource;

class Data extends Home
{

    /**
     * 获取分类列表以及第一个分类的课程
     */
    public function getCate()
    {
        $cateData = parse_attr(module_config('course.category'));
        $cate = [];
        $cid = 0;
        foreach ($cateData as $k => $v){
            if (empty($cid))$cid = $k;
            $cate['tabnav']['tabitem'][] = [
                'id' => $k,
                'text' => $v,
            ];
        }
        $cate['tabnav']['tabnum'] = count($cate['tabnav']['tabitem']);
        $data = [];
        if (!empty($cid))  $data = CourseList::getResource($cid);

        $cate['data'] = $data;
        return json_encode($cate);
    }

    /**
     * 获取课程数据
     */
    public function getList($cid=null)
    {
        $cid = $cid ? $cid : 0;
        if (empty($cid)) {
            if ($this->request->isPost()) {
                $data = $this->request->post();
                if (!empty($data['catId'])) $cid = $data['catId'];
            }
        }
        $data = CourseList::getResource($cid);
        return json_encode($data);
    }

    /**
     * 获取音频信息
     */
    public function getResource()
    {
        if ($this->request->isPost()){
            $data = $this->request->post();
            $data = CourseResource::get($data['resid']);
            $pid = CourseList::where('id',$data['pid'])->value('image');

            if (!empty($data)){
                $data['image'] = get_file_path($pid);
                $data['file'] = get_file_path($data['file']);
            }
            return json_encode($data);
        }
    }
}