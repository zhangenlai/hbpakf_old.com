<?php

namespace app\course\validate;

use think\Validate;

class CourseResource extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */
    protected $rule = [
        'name|资源名称' => 'require',
        'type|资源类型' => 'require',
        'file|文件' => 'require',
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */
    protected $message = [];
}
