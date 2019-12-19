<?php

namespace app\course\validate;

use think\Validate;

class CourseList extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
	protected $rule = [
	    'title|课程名称' => 'require|unique:course_list',
        'image|课程图片' => 'require',
        'cid|课程分类' => 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
}
