<?php
/**
 * 模块信息
 */
return [
    // 模块名[必填]
    'name'        => 'wechat',
    // 模块标题[必填]
    'title'       => '微信',
    // 模块唯一标识[必填]，格式：模块名.开发者标识.module
    'identifier'  => 'wechat.zhang.module',
    // 开发者[必填]
    'author'      => 'ZhangEnLai',
    // 版本[必填],格式采用三段式：主版本号.次版本号.修订版本号
    'version'     => '1.0.0',
    // 模块描述[必填]
    'description' => '微信模块',
    'need_module' => [],
    'need_plugin' => [],
    'tables' => [
        'we_material',
        'we_reply',
    ],
    'database_prefix' => 'dp_',
    'config' => [
        [
            'text',
            'name',
            '公众号名称',
            '自行扩展时使用',
            '',
        ],
        [
            'text',
            'id',
            '公众号原始ID',
            '自行扩展时使用',
            '',
        ],
        [
            'text',
            'number',
            '微信号',
            '自行扩展时使用',
            '',
        ],
        [
            'text',
            'app_id',
            'AppID',
            '',
            '',
        ],
        [
            'text',
            'secret',
            'AppSecret',
            '',
            '',
        ],
        [
            'text',
            'token',
            'Token',
            '',
            '',
        ],
        [
            'text',
            'aes_key',
            'EncodingAESKey',
            '安全模式下请一定要填写',
            '',
        ],
        [
            'text',
            'merchant',
            '商户号',
            '例:1486570641',
            '',
        ],
        [
            'text',
            'key',
            '商户支付API-KEY',
            '商户平台API中生成密钥',
            '',
        ],
        [
            'select',
            'type',
            '微信号类型',
            '自行扩展时使用',
            [
                '订阅号',
                '服务号',
            ],
            '0',
        ],
        [
            'radio',
            'debug',
            'Debug模式',
            '关闭时，不记录微信日志。日志路径 <code>/runtime/log/wechat/easywechat.log</code>',
            [
                '开启',
                '关闭',
            ],
            '1',
        ],
    ],
    'action' => [],
    'access' => [],
];