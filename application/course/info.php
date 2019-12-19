<?php
/**
 * 模块信息
 */
return [
    // 模块名[必填]
    'name'        => 'course',
    // 模块标题[必填]
    'title'       => '课程',
    // 模块唯一标识[必填]，格式：模块名.开发者标识.module
    'identifier'  => 'course.zhang.module',
    // 开发者[必填]
    'author'      => 'ZhangEnLai',
    // 版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
    'version'     => '1.0.0',
    // 模块描述[必填]
    'description' => '课程模块',
    // 模块参数配置
    'config' => [
    // 数组配置复制下去就行
    //['类型', '字段', '默认', '内容', 0],
        ['textarea', 'category', '课程分类', '', ''],
        ['textarea', 'type', '资源类型', '', ''],
    ],

    // 行为配置
    'action' => [
    //行为 复制下去就行
        [
            'module' => 'course',
            'name' => 'list_add',
            'title' => '添加文章',
            'remark' => '添加文章',
            'log' => '[user|get_nickname] 在[time|format_time]添加了文章',
            'status' => 1,
        ],
        [
            'module' => 'course',
            'name' => 'list_up',
            'title' => '修改文章',
            'remark' => '修改文章',
            'log' => '[user|get_nickname] 在[time|format_time]修改了文章：[details]',
            'status' => 1,
        ],

    ],
];