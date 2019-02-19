<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WeixinUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;
use GuzzleHttp;

class WeixinController extends Controller
{
    //

    protected $redis_weixin_access_token = 'str:weixin_access_token';     //微信 access_token

    public function test()
    {
        //echo __METHOD__;
        //$this->getWXAccessToken();
        $this->getUserInfo(1);
    }

    /**
     * 首次接入
     */
    public function validToken1()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        echo $_GET['echostr'];
    }

    /**
     * 接收微信服务器事件推送
     */
    public function wxEvent()
    {
        $data = file_get_contents("php://input");

        //解析XML
        $xml = simplexml_load_string($data);        //将 xml字符串 转换成对象

        $event = $xml->Event;                       //事件类型
        $openid = $xml->FromUserName;               //用户openid
        //判断事件类型
        if($event=='subscribe'){                        //扫码关注事件

            $sub_time = $xml->CreateTime;               //扫码关注时间


            echo 'openid: '.$openid;echo '</br>';
            echo '$sub_time: ' . $sub_time;

            //获取用户信息
            $user_info = $this->getUserInfo($openid);
            echo '<pre>';print_r($user_info);echo '</pre>';

            //保存用户信息
            $u = WeixinUser::where(['openid'=>$openid])->first();
            //var_dump($u);die;
            if($u){       //用户不存在
                echo '用户已存在';
            }else{
                $user_data = [
                    'openid'            => $openid,
                    'add_time'          => time(),
                    'nickname'          => $user_info['nickname'],
                    'sex'               => $user_info['sex'],
                    'headimgurl'        => $user_info['headimgurl'],
                    'subscribe_time'    => $sub_time,
                ];

                $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                var_dump($id);
            }
        }elseif($event=='CLICK'){
            if($xml->EventKey=='kefu01'){
                $this->kefu01($openid,$xml->ToUserName);
            }
        }

        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 客服处理
     * @param $openid   用户openid
     * @param $from     开发者公众号id 非 APPID
     */
    public function kefu01($openid,$from)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. 'Hello World, 现在时间'. date('Y-m-d H:i:s') .']]></Content></xml>';
        echo $xml_response;
    }



    /**
     * 接收事件推送
     */
    public function validToken()
    {
        //$get = json_encode($_GET);
        //$str = '>>>>>' . date('Y-m-d H:i:s') .' '. $get . "<<<<<\n";
        //file_put_contents('logs/weixin.log',$str,FILE_APPEND);
        //echo $_GET['echostr'];
        $data = file_get_contents("php://input");
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);
    }

    /**
     * 获取微信AccessToken
     */
    public function getWXAccessToken()
    {

        //获取缓存
        $token = Redis::get($this->redis_weixin_access_token);
        if(!$token){        // 无缓存 请求微信接口
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WEIXIN_APPID').'&secret='.env('WEIXIN_APPSECRET');
            $data = json_decode(file_get_contents($url),true);

            //记录缓存
            $token = $data['access_token'];
            Redis::set($this->redis_weixin_access_token,$token);
            Redis::setTimeout($this->redis_weixin_access_token,3600);
        }
        return $token;

    }

    /**
     * 获取用户信息
     * @param $openid
     */
    public function getUserInfo($openid)
    {
//        $openid = 'oLreB1jAnJFzV_8AGWUZlfuaoQto';
        $access_token = $this->getWXAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $data = json_decode(file_get_contents($url),true);
//        echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }


    /**
     *创建服务号菜单
     */
    public function createMenu(){
        //1.获取access_token  拼接请求接口
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->getWXAccessToken();
//        echo $url;die;

        //2.请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            'button'=>[
                [
                    'name'=>'我',
                    'sub_button'=>[
                        [
                            "type"  => "click",      // click类型
                            "name"  => "客服01",
                            "key"   => "kefu01"
                        ]
                    ]
                ],
                [
                    'name'=>'爱',
                    'sub_button'=>[
                        [
                            'type'=>"view",
                            'name'=>"喜",
                            'url'=>"https://www.baidu.com"
                        ],
                        [
                            'type'=>"view",
                            'name'=>"欢",
                            'url'=>"https://www.baidu.com"
                        ],
                    ]
                ],
                [
                    'name'=>'你',
                    'sub_button'=>
                    [
                        [
                            'type'=>"view",
                            'name'=>"额",
                            'url'=>"https://www.baidu.com"
                        ],
                        [
                            "type"  => "click",      // click类型
                            "name"  => "客服02",
                            "key"   => "kefu01"
                        ]
                    ]

                ]
            ]
        ];
//        print_r($data);die;
        $r=$client->request('POST',$url,[
            'body'=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);

//        var_dump($r);
        //3.解析微信接口返回信息
        $response_arr=json_decode($r->getBody(),true);

//        echo '<pre>';print_r($response_arr);echo '</pre>';

        if($response_arr['errcode']==0){
            echo "菜单创建成功";
        }else{
            echo "菜单创建失败";echo "<br>";
            echo $response_arr['errmsg'];
        }
    }

}