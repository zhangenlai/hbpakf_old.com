<?php
namespace app\poetry\admin;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use app\poetry\model\PluginPoetry;
use think\Session;

class Getpoetry extends Command
{
    protected function configure()
    {
        $this->setName('getpoetry')->setDescription('Here is the remark ');
    }
    protected function execute(Input $input, Output $output)
    {
        $this->getpoetry();
        $output->writeln('成功执行');//date("Y-m-d H:i:s",time()-30*60)
    }
    public function getpoetry()
    {
        $token = '45AwtRZJGQ0E+qQ5vk+MXRWm765yaSiU';    //token获取一次即可永久使用 (上方获取token接口以便不时之需)

        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>"X-User-Token:".$token,
            )
        );
        $context = stream_context_create($opts);
        $url = 'https://v2.jinrishici.com/sentence';    //获取诗词信息
        $result = file_get_contents($url,false,$context);
        $result = json_decode($result,true);
        if ($result['status'] == 'success'){    //调用成功
            $resData = $result['data'];
            $origin = $resData['origin'];
            $insData = [
                'title' => $origin['title'],
                'dynasty' => $origin['dynasty'],
                'author' => $origin['author'],
                'content' => implode('#',$origin['content']),
            ];
            if (!empty($origin['translate'])){
                $insData['translate'] = implode('#',$origin['translate']);
            }
            if (!PluginPoetry::get($insData)){
                $insData['recomment'] = $resData['content'];
                PluginPoetry::create($insData);
            }
        }
    }

    /**
     * 获取token (不时之需)
     */
    public function getToken()
    {
        $tokenUrl = 'https://v2.jinrishici.com/token';
        $tokenRes = file_get_contents($tokenUrl);
        $tokenRes = json_decode($tokenRes,true);
        $token = $tokenRes['data'];
    }
}