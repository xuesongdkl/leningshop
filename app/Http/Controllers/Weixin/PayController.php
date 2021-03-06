<?php

namespace App\Http\Controllers\Weixin;

use App\Model\GoodsModel;
use App\Model\OrderModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class PayController extends Controller
{
    public $weixin_unifiedorder_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    public $weixin_notify_url = 'http://xsdkl.52self.cn/weixin/pay/notice';     //支付通知回调

    //下单
    public function test($order_id){
        $order=OrderModel::where(['order_id'=>$order_id])->first();
//       echo $order['order_sn'];die;
        $total_fee=1;       //用户要支付的总金额

        $order_info=[
            'appid'           =>  env('WEIXIN_APPID_0'),     //微信支付绑定的服务号的APPID
            'mch_id'          =>  env('WEIXIN_MCH_ID'),     //商户ID
            'nonce_str'       =>  str_random(16),       //随机字符串
            'sign_type'       =>  'MD5',
            'body'            =>  '测试订单-'.$order['order_sn'],
            'out_trade_no'    =>  $order['order_sn'],          //本地订单号
            'total_fee'       =>  $total_fee,
            'spbill_create_ip'=>  $_SERVER['REMOTE_ADDR'],    //客户端IP
            'notify_url'      =>  $this->weixin_notify_url,        //通知回调地址
            'trade_type'      =>  'NATIVE'                         //交易类型
        ];

        $this->value =[];
        $this->values= $order_info;
        $this->SetSign();

        $xml=$this->ToXml();         //将数组转换成XML
       //echo $xml;exit;
        $rs=$this->postXmlCurl($xml,$this->weixin_unifiedorder_url,$useCert = false,$second = 30);

//        var_dump($rs);exit;
        $data=simplexml_load_string($rs);
//        echo $data->return_code;
//        echo $data->return_msg;die;
        $r=$data->code_url;//二维码路径
        $re=[
            'r'=>$r,
            'order_id'=>$order_id
        ];
        return view('weixin.pay',$re);
        //将 code_url 返回给前端，前端生成 支付二维码
    }


    protected function ToXml(){
        if(!is_array($this->values)
            || count($this->values) <= 0)
        {
            die("数组数据异常！");
        }
        $xml = "<xml>";
        foreach ($this->values as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    private function postXmlCurl($xml,$url,$useCert=false,$second = 30){
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
//        var_dump($data);die;
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            die("curl出错，错误码:$error");
        }
    }

    public function SetSign(){
        $sign = $this->MakeSign();
        $this->values['sign']=$sign;
        return $sign;
    }

    private function MakeSign(){
        //签名步骤一：按字典序排序参数
        ksort($this->values);
        $string = $this->ToUrlParams();
        //签名步骤二：在string后加入KEY
        $string=$string."&key=".env('WEIXIN_MCH_KEY');
        //签名步骤三：MD5加密
        $string=md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    protected function ToUrlParams(){
        $buff = '';
        foreach($this->values as $k =>$v){
            if($k != 'sign' && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }
        $buff = trim($buff,'&');
        return $buff;
    }


    public function wxSign($data){

        $this->values =[];
        $this->values =$data;

        $sign=$this->SetSign();
        if($sign==$data['sign']){
            return true;
        }else{
            return false;
        }

    }

    /**
     * 微信支付回调
     */
    public function notice(){
        $data=file_get_contents("php://input");
        //记录日志
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";
        file_put_contents('logs/wx_pay_notice.log',$log_str,FILE_APPEND);

        //逻辑处理 订单状态
        $xml= (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        if($xml['result_code']=='SUCCESS'&&$xml['return_code']=='SUCCESS'){   //微信支付成功回调
            $sign=$this->wxSign($xml);
            //验证签名
            if ($sign) {       //签名验证成功
                // 逻辑处理  订单状态更新
                //验证订单交易状态
                //更新订单状态
                $order_sn =$xml['out_trade_no'];
                $where=[
                    'order_sn' =>$order_sn
                ];
                $data = [
                    'is_pay' => 1,       //支付状态  0未支付 1已支付
                    'pay_time'=>time()
                ];
                OrderModel::where($where)->update($data);
            }else{
                //TODO 失败
                echo '验签失败，IP:'.$_SERVER['REMOTE_ADDR'];
                //TODO 记录日志
            }
        }
        $response='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        echo $response;
    }

    //支付成功
    public function qrpay(Request $request){
        $order_id=$request->input('order_id');
        $order_info=OrderModel::where(['order_id'=>$order_id])->first();
        if($order_info['is_pay']==1){
            echo 1;
        }else{
            echo 0;
        }
    }

    public function success(){
        echo "支付成功";
    }
}
