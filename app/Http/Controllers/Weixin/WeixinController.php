<?php

namespace App\Http\Controllers\Weixin;

use App\Model\WeixinMedia;
use App\Model\WeixinUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
use Illuminate\Support\Facades\Storage;

use App\Model\WeixinChatModel;

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

        //记录日志
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_event.log',$log_str,FILE_APPEND);

        $event = $xml->Event;                       //事件类型
        $openid = $xml->FromUserName;               //用户openid



        // 处理用户发送消息
        if(isset($xml->MsgType)){
            if($xml->MsgType=='text'){            //用户发送文本消息
                $msg = $xml->Content;
                //记录聊天消息

                $data = [
                    'msg'       => $msg,
                    'msgid'     => $xml->MsgId,
                    'openid'    => $openid,
                    'msg_type'  => 1 ,       // 1用户发送消息 2客服发送消息
                    'add_time'  => time()
                ];

                $id = WeixinChatModel::insert($data);
//                var_dump($id);
                //$xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. $msg. date('Y-m-d H:i:s') .']]></Content></xml>';


                //echo $xml_response;
            }elseif($xml->MsgType=='image') {    //用户发送图片消息
                //视业务需求是否需要下载保存图片
                if (1) {  //下载图片素材
                    $file_name=$this->dlWxImg($xml->MediaId);
                    $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                    echo $xml_response;

                    //写入数据库
                    $data=[
                        'openid'=>$openid,
                        'add_time'=>time(),
                        'msg_type'=>'image',
                        'media_id'=>$xml->MediaId,
                        'format'=>$xml->Format,
                        'msg_id'=>$xml->MsgId,
                        'local_file_name'=>$file_name
                    ];

                    $m_id=WeixinMedia::insertGetId($data);
                    var_dump($m_id);
                }
            }elseif($xml->MsgType=='voice'){//处理语音消息
                $file_name=$this->dlVoice($xml->MediaId);
                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;

                 //写入数据库
                 $data=[
                    'openid'=>$openid,
                    'add_time'=>time(),
                    'msg_type'=>'voice',
                    'media_id'=>$xml->MediaId,
                    'format'=>$xml->Format,
                    'msg_id'=>$xml->MsgId,
                    'local_file_name'=>$file_name
                ];

                $m_id=WeixinMedia::insertGetId($data);
                var_dump($m_id);

            }elseif($xml->MsgType=='video'){//处理视频消息
                $file_name=$this->dlVideo($xml->MediaId);
                $xml_response = '<xml><ToUserName><![CDATA[' . $openid . ']]></ToUserName><FromUserName><![CDATA[' . $xml->ToUserName . ']]></FromUserName><CreateTime>' . time() . '</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[' . date('Y-m-d H:i:s') . ']]></Content></xml>';
                echo $xml_response;

                 //写入数据库
                 $data=[
                    'openid'=>$openid,
                    'add_time'=>time(),
                    'msg_type'=>'video',
                    'media_id'=>$xml->MediaId,
                    'format'=>$xml->Format,
                    'msg_id'=>$xml->MsgId,
                    'local_file_name'=>$file_name
                ];

                $m_id=WeixinMedia::insertGetId($data);
                var_dump($m_id);
            }elseif($xml->MsgType=='event'){

                if($event=='subscribe'){    //判断事件类型
                    //扫码关注事件

                    $sub_time = $xml->CreateTime;           //扫码关注时间
//                    echo 'openid: '.$openid;echo '</br>';
//                    echo '$sub_time: ' . $sub_time;

                    //获取用户信息
                    $user_info = $this->getUserInfo($openid);
//                    echo '<pre>';print_r($user_info);echo '</pre>';

                    //保存用户信息
                    $u = WeixinUser::where(['openid'=>$openid])->first();
                    //var_dump($u);die;
                    if($u){       //用户不存在
                        echo '此用户已存在';
                    }else {
                        $user_data = [
                            'openid' => $openid,
                            'add_time' => time(),
                            'nickname' => $user_info['nickname'],
                            'sex' => $user_info['sex'],
                            'headimgurl' => $user_info['headimgurl'],
                            'subscribe_time' => $sub_time,
                        ];

                        $id = WeixinUser::insertGetId($user_data);      //保存用户信息
                        //var_dump($id);
                    }
                }elseif($event=='CLICK'){     //点击
                    if ($xml->EventKey == 'kefu01') {
                        $this->kefu01($openid, $xml->ToUserName);
                    }
                }
            }
        }

    }

    /**
     * 客服处理
     * @param $openid   用户openid
     * @param $from     开发者公众号id 非 APPID
     */
    public function kefu01($openid,$from)
    {
        // 文本消息
        $xml_response = '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$from.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. '你好 上帝, 现在时间'. date('Y-m-d H:i:s') .']]></Content></xml>';
        echo $xml_response;
    }

    /**
    *下载图片素材
     * @param $media_id
     */

    public function dlWxImg($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
        //echo $url;echo '</br>';

        //保存图片
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();
        //echo '<pre>';print_r($h);echo '</pre>';die;
//        var_dump($response);exit;
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
//        var_dump($file_info);exit;

        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/images/'.$file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
//            echo 'OK';
        }else{      //保存失败
            //echo 'NO';
        }

        return $file_name;
    }

    /**
    *下载语音文件
     * @param $media_id
     */
    public function dlVoice($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
//        echo $url;echo '</br>';die;

        //保存图片
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
//        echo $response->getBody();die;
        //$h = $response->getHeaders();
        //echo '<pre>';print_r($h);echo '</pre>';die;

        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
//        var_dump($file_info);exit;

        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/voice/'.$file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
//            echo 'OK';
        }else{      //保存失败
            //echo 'NO';
        }

        return $file_name;
    }

    /**
     *下载视频文件
     * @param $media_id
     */
    public function dlVideo($media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getWXAccessToken().'&media_id='.$media_id;
//        echo $url;echo '</br>';die;

        //保存图片
        $client = new GuzzleHttp\Client();
        $response = $client->get($url);
//        echo $response->getBody();die;
        //$h = $response->getHeaders();
        //echo '<pre>';print_r($h);echo '</pre>';die;

        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
//        var_dump($file_info);exit;

        $file_name = substr(rtrim($file_info[0],'"'),-20);

        $wx_image_path = 'wx/video/'.$file_name;
        //保存图片
        $r = Storage::disk('local')->put($wx_image_path,$response->getBody());
        if($r){     //保存成功
//            echo 'OK';
        }else{      //保存失败
            //echo 'NO';
        }

        return $file_name;
    }

    /**
    *群发消息
     */
    public function sendMsgs(){

        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->getWXAccessToken();

        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $data=[
            "touser"=>[
                "oNiPq5qguCnYBLX4aTasWJzcY6Q0",
                "oNiPq5l_XzoTwS6BUuF5Mk_Cf3o4"
            ],
            "msgtype"=> "text",
            "text"=>[
                "content"=> "杜凯龙是个弟弟"
            ]
//],
//            "filter"=>[
//                "is_to_all"=>true,
//                "tag_id"=>2
//            ],
//            "text"=>[
//                "content"=>"恭喜你，中奖了"
//            ],
//            "msgtype"=>"mpnews",
        ];
        $r=$client->request('POST',$url,[
            'body'=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        //解析微信接口 返回信息
        $response_arr=json_decode($r->getBody(),true);

      // echo '<pre>';print_r($response_arr);echo '</pre>';

        if($response_arr['errcode']==0){
            echo "群发成功";
        }else{
            echo "群发失败";echo "<br>";
            echo $response_arr['errmsg'];
        }

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

    /*
     * 更新access_token**/
    public function refreshToken(){
        Redis::del($this->redis_weixin_access_token);
        echo $this->getWXAccessToken();
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
                    'name'=>'恨',
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

    public function materialTest()
    {
        //echo __METHOD__;echo '</br>';
        echo '<pre>';print_r($_POST);echo '</pre>';echo '</br>';
        echo '<pre>';print_r($_FILES);echo '</pre>';
    }

    /**
     * 上传素材
     */
    public function upMaterial()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'username',
                    'contents' => 'zhangsan'
                ],
                [
                    'name'     => 'media',
                    'contents' => fopen('abc.jpg', 'r')
                ],
            ]
        ]);

        $body = $response->getBody();
        echo $body;echo '<hr>';
        $d = json_decode($body,true);
        echo '<pre>';print_r($d);echo '</pre>';


    }



    public function upMaterialTest($file_path)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->getWXAccessToken().'&type=image';
        $client = new GuzzleHttp\Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name'     => 'media',
                    'contents' => fopen($file_path, 'r')
                ],
            ]
        ]);

        $body = $response->getBody();
        echo $body;echo '<hr>';
        $d = json_decode($body,true);
        echo '<pre>';print_r($d);echo '</pre>';


    }


    /**
     * 获取永久素材列表
     */
    public function materialList()
    {
        $client = new GuzzleHttp\Client();
        $type = $_GET['type'];
        $offset = $_GET['offset'];

        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->getWXAccessToken();

        $body = [
            "type"      => $type,
            "offset"    => $offset,
            "count"     => 20
        ];
        $response = $client->request('POST', $url, [
            'body' => json_encode($body)
        ]);

        $body = $response->getBody();
        echo $body;echo '<hr>';
        $arr = json_decode($response->getBody(),true);
        echo '<pre>';print_r($arr);echo '</pre>';


    }





    public function formShow()
    {

        return view('test.form');

    }

    public function formTest(Request $request)
    {
        //echo '<pre>';print_r($_POST);echo '</pre>';echo '<hr>';
        //echo '<pre>';print_r($_FILES);echo '</pre>';echo '<hr>';

        //保存文件
        $img_file = $request->file('media');
        //echo '<pre>';print_r($img_file);echo '</pre>';echo '<hr>';

        $img_origin_name = $img_file->getClientOriginalName();
        echo 'originName: '.$img_origin_name;echo '</br>';
        $file_ext = $img_file->getClientOriginalExtension();          //获取文件扩展名
        echo 'ext: '.$file_ext;echo '</br>';

        //重命名
        $new_file_name = str_random(15). '.'.$file_ext;
        echo 'new_file_name: '.$new_file_name;echo '</br>';

        //文件保存路径


        //保存文件
        $save_file_path = $request->media->storeAs('form_test',$new_file_name);       //返回保存成功之后的文件路径

        echo 'save_file_path: '.$save_file_path;echo '<hr>';

        //上传至微信永久素材
        $this->upMaterialTest($save_file_path);


    }

    /*
     * 客服接口--发消息视图页面**/
    public function sendCustomMsgsView(){
        $user=  WeixinUser::where(['id'=>2])->first();
        $data=[
            'user'=>$user
        ];
        return view('weixin.custom_msg',$data);
    }


    /*
    * 客服接口--接收消息**/
    public function sendCustomMsgs(){

        $openid=$_GET['openid'];//用户openid

        $pos=$_GET['pos'];   //上次聊天位置

        $msg=WeixinChatModel::where(['openid'=>$openid])->where('id','>',$pos)->first();

        if($msg){
            $response=[
                'errno'=>0,
                'data'=>$msg->toArray()
            ];
        }else{
            $response=[
                'errno'=>50001,
                'data'=>'服务器异常'
            ];
        }
        die( json_encode($response));
    }

    //客服给客户发消息
    public function msgDb(Request $request){
        $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getWXAccessToken();

        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $message=$request->input('msg');
        $openid=$request->input('openid');
        $pos=$request->input('pos');
        $data=[
            "touser"=>$openid,
            "msgtype"=>"text",
            "text"=>
            [
                "content"=>$message
            ]
        ];
        $r=$client->request('POST',$url,[
            'body'=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        //解析微信接口 返回信息
        $response_arr=json_decode($r->getBody(),true);

        if($response_arr){

            $response=[
                $data=[
                    'msg'=>$message,
                    'add_time'=>time(),
                    'openid'=>$openid,
                    'msg_type'=>$pos
                ],
                WeixinChatModel::insertGetId($data),
                'errno'=>0,
                'data'=>$data
            ];
        }else{
            $response=[
                'errno'=>50001,
                'data'=>'服务器异常'
            ];
        }
        die( json_encode($response));

    }

    //微信登录
    public function login(){
        return view('weixin.login');
    }

    /**
    *接收code
     */
    public function getCode(){
        echo '<pre>';print_r($_GET);echo '</pre>';
        $code = $_GET['code'];
        echo 'code: '.$code;die;

        //2 用code换取access_token 请求接口

        $token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxdbf8b766bc40d75f&secret=d8cbf853a7f4e38a55e170da86458a1f&code='.$code.'&grant_type=authorization_code';
        $token_json = file_get_contents($token_url);
        $token_arr = json_decode($token_json,true);
        echo '<hr>';
        echo '<pre>';print_r($token_arr);echo '</pre>';

        $access_token = $token_arr['access_token'];
        $openid = $token_arr['openid'];

        // 3 携带token  获取用户信息
        $user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_json = file_get_contents($user_info_url);

        $user_arr = json_decode($user_json,true);
        echo '<hr>';
        echo '<pre>';print_r($user_arr);echo '</pre>';
  }


}
