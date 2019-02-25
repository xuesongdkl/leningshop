<?php

namespace App\Admin\Controllers;

use App\Model\WeixinUser;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

use Illuminate\Support\Facades\Redis;
use GuzzleHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Model\WeixinChatModel;

class WeixinController extends Controller
{
    use HasResourceActions;

    protected $redis_weixin_access_token = 'str:weixin_access_token';

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        $user_id=$_GET['user_id'];
//        return $content
//            ->header('Create')
//            ->description('description')
//            ->body($this->form());
        $res=WeixinUser::where(['id'=>$user_id])->first();
        return $content
            ->header('Create')
            ->description('description')
            ->body(view('admin.weixin.chat',['user_info'=>$res])->render());

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new WeixinUser);

        $grid->id('Id');
        $grid->uid('Uid');
        $grid->openid('Openid');
        $grid->add_time('Add time');
        $grid->nickname('Nickname');
        $grid->sex('Sex');
        $grid->headimgurl('Headimgurl')->display(function($url){
            return '<img src="'.$url.'">';
        });
        $grid->subscribe_time('Subscribe time');
        $grid->actions(function ($actions) {
            // 获取当前行主键值
            $key=$actions->getKey();
            $actions->prepend('<a href="/admin/wx/wx_user/create?user_id='.$key.'"><i class="fa fa-paper-plane"></i></a>');
        });
        return $grid;
    }


    /*
    * 客服接口--接收消息**/
    public function sendCustomMsgs(Request $request){

        $openid=$request->input('openid');//用户openid

        $pos=$request->input('msg_pos');   //上次聊天位置

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

    public function msgDb(){
        $url='https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->getWXAccessToken();

        //请求微信接口
        $client = new GuzzleHttp\Client(['base_uri' => $url]);
        $message=$_POST['msg'];
        $openid=$_POST['openid'];
        $pos=$_POST['pos'];
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
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(WeixinUser::findOrFail($id));

        $show->id('Id');
        $show->uid('Uid');
        $show->openid('Openid');
        $show->add_time('Add time');
        $show->nickname('Nickname');
        $show->sex('Sex');
        $show->headimgurl('Headimgurl');
        $show->subscribe_time('Subscribe time');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new WeixinUser);

        $form->number('uid', 'Uid');
        $form->text('openid', 'Openid');
        $form->number('add_time', 'Add time');
        $form->text('nickname', 'Nickname');
        $form->number('sex', 'Sex');
        $form->text('headimgurl', 'Headimgurl');
        $form->number('subscribe_time', 'Subscribe time');

        return $form;
    }


}
