<?php
// +----------------------------------------------------------------------
// | 海豚PHP框架 [ DolphinPHP ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 广东卓锐软件有限公司 [ http://www.zrthink.com ]
// +----------------------------------------------------------------------
// | 官方网站: http://dolphinphp.com
// +----------------------------------------------------------------------

namespace app\index\controller;
use GatewayWorker\Lib\Gateway;

/**
 * 前台首页控制器
 * @package app\index\controller
 */
class Index extends Home
{
    public function index()
    {

        $pageContents = '测试测试13212312341234123w测试测试14100212341234123X,测试测试测试15612312341234123R';
        $reg = '/[\d]{17}[\D|\d]/i';
        preg_match_all( $reg , $pageContents , $results );
        $arr = [];

        if  (!empty($results)){
            if (count($results[0]) > 1){
                foreach ($results[0] as $w => $q){
                    array_push($arr,$q);
                }
            }else {
                if (count($results[0]) == 1){
                    array_push($arr, $results[0][0]);
                }
            }
        }
        dump($arr);die;

        return $this->fetch();
    }

    public function index2()
    {
        $data = [
            'type' => 'message',
            'content' => '123',
        ];
        Gateway::sendToAll(json_encode($data));
    }
}
