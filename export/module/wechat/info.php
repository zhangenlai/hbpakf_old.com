<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

/**
 * 模块信息
 */
return [
  'name' => 'wechat',
  'title' => '微信',
  'identifier' => 'wechat.zhang.module',
  'author' => 'ZhangEnLai',
  'version' => '1.0.0',
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
      '選华电子商务',
    ],
    [
      'text',
      'id',
      '公众号原始ID',
      '自行扩展时使用',
      ' gh_f4ad4ec1dae1',
    ],
    [
      'text',
      'number',
      '微信号',
      '自行扩展时使用',
      'xuanhuaShop',
    ],
    [
      'text',
      'app_id',
      'AppID',
      '',
      'wxd37e402c47a1040e',
    ],
    [
      'text',
      'secret',
      'AppSecret',
      '',
      '7cbe66ef012250a95fb2a116bd6cc4bb',
    ],
    [
      'text',
      'token',
      'Token',
      '',
      'zhangToken',
    ],
    [
      'text',
      'aes_key',
      'EncodingAESKey',
      '安全模式下请一定要填写',
      'vOdUelmAqxsvwh5EZBSua7abFn8wFUW5ygUz1QcLvON',
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
      '1',
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
      '0',
    ],
  ],
  'action' => [],
  'access' => [],
];
