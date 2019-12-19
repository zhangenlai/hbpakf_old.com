<?php

namespace app\course\model;

use think\Model;

class CourseList extends Model
{
    public function resource() { //建立一对多关联
        return $this->hasMany('CourseResource', 'pid', 'id'); //关联的模型，外键，当前模型的主键
    }
    
    public static function getResource($cid)
    {
        $data = self::with('resource')->where(['cid'=>$cid,'status'=>1])->select(); // 通过 with 使用关联模型，参数为关联关系的方法名
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $data[$k]['image'] = get_file_path($v['image']);
                foreach ($v['resource'] as $q => $w){
                    $data[$k]['resource'][$q]['type'] = parse_attr(module_config('course.type'))[$data[$k]['resource'][$q]['type']];
                    if ($w['status'] == 0 || $w['type'] != '音频'){
                        unset($data[$k]['resource'][$q]);
                    }
                }
            }
        }
        return $data;
    }
}
