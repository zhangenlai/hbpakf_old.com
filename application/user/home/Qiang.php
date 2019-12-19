<?php
namespace app\user\home;

use think\Model;
use think\helper\Hash;
use think\Db;
use app\index\controller\Home;

class Qiang extends Home
{
    //注册
    public function add()
    {
//        $user = Db::table('qiang_user')->select();
        $post = $this->request->post();
        $post['time'] = time();
        $phone_number = substr($post['phone_number'],-4);
        $post['token'] = md5(time().$phone_number);

        //生成随机邀请码
        $yqm = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $yqm[rand(0,25)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 5;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        $post['yqm'] = $d;
//        echo $post['token'];die;
//        var_dump($post);
        if(!empty($post['name'])){
            $user_name = Db::name('qiang_user')->where('name',$post['name'])->find();
            if($user_name){
                $msg = [
                    'code'=>'101',
                    'msg'=>'账号已注册',
                ];
                $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
                return $msg;
            }else{
                if($post['yqr'] == ''){
                    $post['ypr'] = '';
                }
                if(Db::name('qiang_user')->insert($post)){
                    $message = [
                        'code'=>'200',
                        'msg'=>'注册成功',
                    ];
                    $message['result']['token'] = $post['token'];
                    $message = json_encode($message,JSON_UNESCAPED_UNICODE);
                    return $message;
                }else{
                    return 'ERROR';
                }
            }
        }else{
            echo "账号未填写";
        }
    }

    //登陆
    public function login(){
        $post = $this->request->post();
        $result = Db::name('qiang_user')->where('name',$post['name'])->find();
//        var_dump($result);
        if($result){
            if($post['pwd']!=$result['pwd']){
                $msg = [
                    'code'=>'101',
                    'msg'=>'密码错误',
                ];
                $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
                return $msg;
            }elseif($post['pwd']=$result['pwd']){
                $msg = [
                    'code'=>'200',
                    'msg'=>'登陆成功',
                ];
                $msg['result']['token'] = $result['token'];
                $msg['result']['isvip'] = $result['isvip'];
                $msg['result']['starttime'] = $result['starttime'];
                $msg['result']['phone_id'] = $result['phone_id'];
                $msg['result']['phone_number'] = $result['phone_number'];
                $msg['result']['yqr'] = $result['yqr'];
                $msg['result']['yqm'] = $result['yqm'];
                $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
                return $msg;
            }
        }else{
            $msg = [
                'code'=>'102',
                'msg'=>'不存在的账户',
            ];
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }
    }

    //续费成功
    

    //查询推送消息
    public function select(){
        $result = Db::name('qiang_user')->where('id','1')->find();
        $msg = [
            'code'=>'200',
            'msg'=>'查询成功',
        ];
        $msg['result']['string'] = $result['ts'];
        $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
        return $msg;
    }

    //修改推送消息
    public function update($ts){
       $result = Db::name('qiang_user')->update(['ts' => $ts,'id'=>1]);
        $res = Db::name('qiang_user')->where('id','1')->find();
        if($result){
            $msg = [
                'code'=>'200',
                'msg'=>'修改成功',
            ];
            $msg['result']['string'] = $res['ts'];
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }
    }

    //根据token查用户信息
    public function select_token(){
        $post = $_POST;
        $result = Db::name('qiang_user')->where('token',$post['token'])->find();
        $msg = [
            'code'=>'200',
            'msg'=>'查询成功',
        ];
        $msg['result']['token'] = $result['token'];
        $msg['result']['isvip'] = $result['isvip'];
        $msg['result']['starttime'] = $result['starttime'];
        $msg['result']['phone_id'] = $result['phone_id'];
        $msg['result']['phone_number'] = $result['phone_number'];
        $msg['result']['yqr'] = $result['yqr'];
        $msg['result']['yqm'] = $result['yqm'];
        $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
        return $msg;
    }

    //抓取vip首页面展示
    public function ceshi(){
        $url = 'http://gzysapi.apptodev.com/api/v2/home/index/';
        $sl_data=array(
            'version_code'=>'402',
            'uid'=>'52899',
            'imei'=>'c95ff019dea37c038c370a126612d788',
            'sign'=>'3a5fad2ecf7a614f8684f4d29d149589',
            'timestamp'=>'1568973935',
            'token'=>'f9b6a9cf93717563a6254edc80c47bec',
            'randcode'=>'491501',
            'umeng_version'=>'www',
            'devtype'=>'1',
            'origin_imei'=>'860921046028185',
        );
//        $url = 'https://www.hbpakf.com/index.php/user/qiang/select_token';
//        $sl_data=array(
//            'token'=>'a7409e7bd110a6923bf1e463cec9981d',
//        );
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);//要访问的地址
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);//执行结果是否被返回，0是返回，1是不返回
//        curl_setopt($ch, CURLOPT_POST, 1);// 发送一个常规的POST请求
//        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sl_data));
//        $output = curl_exec($ch);//执行并获取数据
//
//            $output = json_decode($output,true);
//        var_dump($output);
//        echo $output;
//        curl_close($ch);
        $result = json_decode($this->httpPost($url,$sl_data),true);
        var_dump($result);
        $res = $result['vipweb'];
//        var_dump($res);
//        $res = Db::name('qiang_user')->insert($res);

        //循环入库二维数组
//        foreach($res as $k=>$v){
//            $ress = Db::name("qiang_data2")->insert($v);
//        }

    }

    //post调取接口
    public function httpPost($url,$param,$post_file=false){
        $oCurl = curl_init();
        if(stripos($url,"https://")!==FALSE){
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
        }
        if (is_string($param) || $post_file) {
            $strPOST = $param;
        } else {
            $aPOST = array();
            foreach($param as $key=>$val){
                $aPOST[] = $key."=".urlencode($val);
            }
            $strPOST =  join("&", $aPOST);
        }
        curl_setopt($oCurl, CURLOPT_URL, $url);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt($oCurl, CURLOPT_POST,true);
        curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
        $sContent = curl_exec($oCurl);
        $aStatus = curl_getinfo($oCurl);
        curl_close($oCurl);
        if(intval($aStatus["http_code"])==200){
            return $sContent;
        }else{
            return false;
        }
    }

    //抓取数据
    public function pdata(){
        $url = 'http://gzysapi.apptodev.com/api/web/loadjs/';
        $time = time();
        $sl_data=array(
            'version_code'=>'402',
            'uid'=>'52899',
            'sign'=>'a9e0bef97c03e6a40b014cc2262304b4',
            'vweb'=>'27',
            'timestamp'=>$time,
            'token'=>'f9b6a9cf93717563a6254edc80c47bec',
            'randcode'=>'047930',
            'umeng_version'=>'www',
            'devtype'=>'1',
            'loadtype'=>'3',
        );
        $result = json_decode($this->httpPost($url,$sl_data),true);
//        var_dump($result);
        $data = $result['data'];
        var_dump($data);
//        $ress = Db::name("qiang_data3")->insert($data);
    }

    //token验证 查数据
    public function token_data(){
        $post = $_POST;
        $user_name = Db::name('qiang_user')->where('token',$post['token'])->find();
        if($user_name){
            $res = Db::name('qiang_data2')->select();
            for($i=0;$i<=11;$i++){
                $result['i'.$i] = $res[$i];
            }
            $result['code'] = '200';
            $result['msg'] = 'token验证成功';
            $msg = json_encode($result,JSON_UNESCAPED_UNICODE);
            return $msg;
        }else{
            $msg = [
                'code'=>'102',
                'msg'=>'token验证失败',
            ];
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }
    }

    public function token_data2(){
        $post = $_POST;
        $res = Db::name('qiang_code')->where('id','1')->find();
        if($res['code']==1){
            $user_name = Db::name('qiang_user')->where('token',$post['token'])->find();
            if($user_name){
                $result = Db::name('qiang_data3')->where('js_2',$post['name'])->find();
                $result['code'] = '200';
                $result['msg'] = 'token验证成功';
                $msg = json_encode($result,JSON_UNESCAPED_UNICODE);
                return $msg;
            }else{
                $msg = [
                    'code'=>'102',
                    'msg'=>'token验证失败',
                ];
                $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
                return $msg;
            }
        }elseif($res['code']==0){
            $msg = [
                'code'=>'200',
                'msg'=>'暂无数据哦',
            ];
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }

    }

    public function token_code(){
        $res = Db::name('qiang_code')->where('id','1')->find();
        $result['code'] = '200';
        $result['msg'] = '查询状态值成功';
        $result['state'] = $res['code'];
        $msg = json_encode($result,JSON_UNESCAPED_UNICODE);
        return $msg;
    }

    public function update_code(){
        $data = $_GET;
        $res = Db::table('dp_qiang_code')->where('id', 1)->update(['code' => $data['code']]);
        $ress = Db::name('qiang_code')->where('id','1')->find();
        $result['code'] = '200';
        $result['msg'] = '修改状态值成功';
        $result['state'] = $ress['code'];
        $msg = json_encode($result,JSON_UNESCAPED_UNICODE);
        return $msg;
    }

    //卡密
    public function ka_insert(){
        $post = $_GET;
        $shu = $post['shu'];
        for($i=0;$i<$shu;$i++){
        $yqm = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $yqm[rand(0,16)]
            .strtoupper(dechex(date('m')))
            .date('d').substr(time(),-5)
            .substr(microtime(),2,5)
            .sprintf('%02d',rand(0,99));
        for(
            $a = md5( $rand, true ),
            $s = '0123456789ABCDEFGHIJKLMNOPQRSTUV',
            $d = '',
            $f = 0;
            $f < 8;
            $g = ord( $a[ $f ] ),
            $d .= $s[ ( $g ^ ord( $a[ $f + 8 ] ) ) - $g & 0x1F ],
            $f++
        );
        $d = md5($d);
        $d = substr($d,16);
        $post['km'] = $d;
        $post['status'] = '0';

            Db::name('qiang_km')->insert($post);
        }
    }

    //卡密使用
    public function ka_select(){
        $post = $_POST;
        $res = Db::name('qiang_km')->where('km',$post['km'])->find();
//        var_dump($res);
        if($res['status']=='0'){
            $result = Db::name('qiang_km')->where('km',$res['km'])->update(['status' => '1']);
            $msg = [
                'code'=>'200',
                'msg'=>'卡密使用成功',
            ];
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }else{
            $msg = [
                'code'=>'200',
                'msg'=>'卡密使用失败，此卡密已使用过，请联系管理员',
            ];
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }
    }

    //查询固定条数的卡密
    public function select_km(){
        $post = $_POST;
        $res = Db::name('qiang_km')->select();
//        echo "<pre>";
//        var_dump($res);
        $res = array_slice($res,1,$post['tiao']);
        $msg = [
            'code'=>'200',
            'msg'=>$res,
        ];
        $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
        return $msg;
    }

    //查询单条卡密 
    public function km_find(){
        $post = $_POST;
        $res = Db::name('qiang_km')->where('km',$post['km'])->find();
        if ($res){
            $msg = [
                'code'=>'200',
                'msg'=>$res,
            ];
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }else{
            $msg = [
                'code'=>'200',
                'msg'=>'没有此卡密',
            ];
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }
    }

    public function zhuce(){
        $post = $_POST;
        if(!empty($post['tel']) and !empty($post['code'])){
            $res = Db::name('ce')->insert($post);
            if($res){
//                echo "<script>alert('注册成功');</script>";
                setcookie("user", $post['tel']);
                $data['name'] = $post['tel'];
                return json_encode($data,true);
            }
        }else{
            echo "<script>alert('注册失败');</script>";
        }
    }

    public function denglu(){
        $post = $this->request->post();
        $result = Db::name('ce')->where('tel',$post['tel'])->find();
//        var_dump($result);
        if($result){
            if($post['code']!=$result['code']){
                $msg = [
                    'code'=>'101',
                    'msg'=>'密码错误',
                ];
//                $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
//                return $msg;
//                echo "<script language=\"javascript\"> alert(\"密码错误\"); window.history.back(-1); </script> ";

                return json_encode($msg,true);
            }elseif($post['code']=$result['code']){
                $msg = [
                    'code'=>'200',
                    'msg'=>'登陆成功',
                ];
                setcookie("user", $result['tel']);
                $data['name'] = $result['tel'];
                $data = json_encode($data,true);
                return $data;
            }
        }else{
            $msg = [
                'code'=>'102',
                'msg'=>'不存在的账户',
            ];
            echo "<script language=\"javascript\"> alert(\"用户不存在\"); window.history.back(-1); </script> ";
            $msg = json_encode($msg,JSON_UNESCAPED_UNICODE);
            return $msg;
        }
    }
    
    public function shishi(){
        $data['msg'] = '1';
        return json_encode($data);
    }

}
