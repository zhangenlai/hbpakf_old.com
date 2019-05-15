<?php

namespace app\wechat\admin;

use app\admin\controller\Admin;
use app\common\builder\ZBuilder;
use think\DB;
use Doctrine\Common\Cache\RedisCache;
use EasyWeChat\Foundation\Application;
/*
 * 自动回复管理
 */

class Tgewm extends Admin
{
    // 列表
    public function index()
    {


        // 数据列表
        $data_list = DB::table('ien_tgewm')->order('id desc')->paginate();
        $btn_access = [
            'title' => '新增',
            'icon'  => 'fa fa-plus-circle',
            'class' => 'btn btn-primary',
            'href'  => url('add')
        ];
        // 分页数据
        $page = $data_list->render();
        // 使用ZBuilder快速创建数据表格
        return ZBuilder::make('table')
            ->setPageTitle('二维码列表')// 设置页面标题
          	->hideCheckbox()
            ->setTableName('tgewm')// 设置数据表名
            ->setSearch(['id' => 'ID', 'tgid' => '推广渠道ID'])// 设置搜索参数
            ->addColumns([ // 批量添加列
                ['id', 'ID'],
                ['images', '推广二维码','img_url'],
                ['tgid', '推广渠道ID'],
              	['beizhu', '渠道说明'],
                ['addtime', '创建日期', 'datetime'],
                ['endtime', '失效日期', 'callback', function($value, $data){
                    if($data['yxq']==1)
                    {
                    	return "长期";
                    }
                  else{
                      if ($data['endtime'] < time()) {
                          return '<span class="font-w600 text-danger" style="text-decoration: line-through; ">' . date('Y-m-d', $data['endtime']) . '</span>';
                      } else {
                          return '<span class="font-w600 text-success">' . date('Y-m-d', $data['endtime']) . '</span>';
                      }
                  }
                }, '__data__'],
                ['yxq','有效期','','',['0'=>'临时','1'=>'长期']],
              	
                ['right_button', '操作', 'btn']
            ])
            ->addTopButton('custom', $btn_access,[], true)
            ->addRightButtons('delete')// 批量添加右侧按钮
            ->setRowList($data_list)// 设置表格数据
            ->setPages($page)// 设置分页数据
            ->fetch(); // 渲染页面
    }

    // 添加自动回复
    public function add()
    {
        // 保存数据
        if ($this->request->isPost()) {
            $data = $this->request->post();
            if (empty($data['beizhu'])){
                return $this->error('渠道说明不能为空');
            }
          	if(empty($data['tgid'])){
            	return $this->error('请填写渠道ID并选择有效期!');
            }
          	if  (!empty($data['linshi']) && $data['linshi'] > 30){
                return $this->error('最大天数不得超过30天!');
            }
     		$ishas=DB::table('ien_tgewm')->where('tgid',$data['tgid'])->find();
          	if(!empty($ishas))
            {
            	$this->error('推广ID重复!');
            }
          	$insert['tgid']=$data['tgid'];	
          	$insert['addtime']=time();
          	$insert['beizhu']=$data['beizhu'];	
          	$insert['yxq']=$data['yxq'];
            $insert['sucaiid']=0;
          	if (!empty($data['sucaiid'])){
                $insert['sucaiid']=$data['sucaiid'];
            }
          	if($data['yxq']==0)
            {
            	$insert['endtime']=time()+60*60*24*$data['linshi'];     //65535*30
            }
            else{
				$insert['endtime']=time()+60*60*24*365;                    //65535*365
            }
          
          	$config=module_config('wechat');	
          	$cacheDriver = new RedisCache();
            // 创建 redis 实例
            $redis = new \Redis();
            $redis->connect('localhost', 6379);
            $cacheDriver->setRedis($redis); 
            $config2['cache']=$cacheDriver;
            $config = array_merge($config, $config2);
            $app = new Application($config);
          	//$userService = $app->user;
          //	$a=$userService->get("oIgNuwtJJecTJ8dnvpdoNF03bj9I");
          //	dump($a);
          //die;
          
            $accessToken = $app->access_token;
          	$token = $accessToken->getToken();
          
          	//$token = $this->app->access_token;
          if($data['yxq']==1)
          {
          	//永久
            $send['action_name']="QR_LIMIT_STR_SCENE";
          }
          else{
          	//临时
            $send['expire_seconds']=60*60*24*$data['linshi'];        //30天的秒
            $send['action_name']="QR_STR_SCENE";
          }


            $dataa['tgid']=$insert['tgid'];
            if (!empty($insert['sucaiid'])){
                $dataa['sucaiid']=$insert['sucaiid'];
            }
            $send['action_info']['scene']['scene_str']=base64_encode(json_encode($dataa));
            $insertID = db::table('ien_tgewm')->insertGetId($insert);
//            dump($send);die;
            $postdata=json_encode($send);
//            $this->logsave($postdata);
            $geturl="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$token;

            $ch = curl_init($geturl);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            $result = curl_exec($ch);

            $jsoninfo=json_decode($result,true);
            $ticket=$jsoninfo['ticket'];
			if(empty($ticket))
            {
              //生成失败，返回常规二维码
               return $this->error('生成二维码错误！',null, '_parent_reload');
            }
            else{
                $ewm = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket;
            }
            DB::table('ien_tgewm')->where('id',$insertID)->update(['images' => $ewm]);
            $this->success('创建成功',null, '_parent_reload');
          
          
        }
      
      	$is=DB::table('ien_tgewm')->order("id desc")->find();
      	$tgid=$is['id']+1;


        return ZBuilder::make('form')
            ->addFormItems([ // 批量添加表单项
                ['hidden', 'tgid',rand(10,99).$tgid],
                ['radio', 'yxq', '有效期', '', ['0'=>'临时', '1'=>'永久'], 0],
              	['number','linshi','临时有效期(天数)','','30'],
                ['text', 'beizhu', '渠道说明'],
              	['text','sucaiid','扫码自动回复内容(素材ID)','请核对素材ID是否正确，提交后将无法修改（如不输入素材ID，则会自动回复关注提示信息）'],
            ])
            ->setTrigger('yxq','0','linshi')
            ->layout(['linshi'=>4,'yxq'=>4])
            ->fetch();
    }
    //创建日志
    function createFolder($path) {
        if (!file_exists($path)) {
//            createFolder(dirname($path));
            mkdir($path, 0777);
        }
    }
    //日志
    function logsave($content)
    {
        // global $host_url;
        if (!file_exists(APP_PATH . 'data/log')) {
            $this->createFolder(APP_PATH . 'data/log');
        }

        if (!file_exists(APP_PATH . 'data/log/' . date('Y-m-d') . '.txt')) {
            $f = fopen(APP_PATH . 'data/log/' . date('Y-m-d') . '.txt', "w+");
            chmod(APP_PATH . 'data/log/' . date('Y-m-d') . '.txt', 0755);
            fclose($f);
        }

        if (!empty($content)) {
            $content = "\r\n" . date('Y-m-d H:i:s') . "：" . $content;
            file_put_contents(APP_PATH . 'data/log/' . date('Y-m-d') . '.txt', $content, FILE_APPEND);
        }
    }
   
}