<?php
namespace app\wechatsma\home;

use app\index\controller\Home;
use think\Db;
use think\Session;
use think\Env;

class Minsu extends Home
{
    public function index()
    {
        $product = [
            'service' => 'create_instant_trade',
            'mch_id' => '15151515',
            'charset' => 'UTF-8',
            'sign_type' => 'MD5',
            'version' => '1.0',
            'call_back_url' => 'https://www.hbpakf.com/index.php/wechatsma/minsu/callback',
            'store_name' => '测试店铺名称',
            'recevice_id' => '15151515',      //收款方唯一标识(接入平台商户编号)
            'mch_order_no' => '12341234',    //订单号
            'pay_way' => 'JSAPI',
            'pay_channel' => 'WXPAY',
            'subOpenId' => 'opgldajwdqwd',
            'currency' => 'AUD',
            'pay_amount' => '100',
            'goods_name' => '商品名称',
            'goods_price' => '100',
            'quantity' => '1',
            'goods_desc' => '商品描述',
            'timeout_express' => '90m'
        ];
        $product['sign'] = md5($this->ASCII($product));
        $datajsonstr=json_encode($product,JSON_UNESCAPED_SLASHES);

        $url = 'https://gate.supaytechnology.com/api/gateway/merchant/order';
        $this->curlRequest($url,$datajsonstr);
    }

    /**
     * ascii 排序参数
     */
    function ASCII($params = array()){
        if(!empty($params)){
            $p =  ksort($params);
            if($p){
                $str = '';
                foreach ($params as $k=>$val){
                    if (!empty($val)) {
                        $str .= $k . '=' . $val . '&';
                    }
                }
                $strs = rtrim($str, '&');
                return $strs;
            }
        }
        return '参数错误';
    }

    public function curlRequest($url,$data)
    {
        $curl = curl_init();
        $this_header = array(
            "content-type: application/x-www-form-urlencoded;
	charset=UTF-8"
        );
        curl_setopt($curl,CURLOPT_HTTPHEADER,$this_header);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        $tmpInfo = curl_exec($curl);     //返回api的json对象
        dump($tmpInfo);die;
        //关闭URL请求
        curl_close($curl);
    }
}