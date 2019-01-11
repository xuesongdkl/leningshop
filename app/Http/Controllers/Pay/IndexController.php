<?php

namespace App\Http\Controllers\Pay;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;

class IndexController extends Controller
{
    public  function index(){

    }
    //订单支付
    public  function pay($order_id){
        //查看订单
        $order=OrderModel::where(['order_id'=>$order_id])->first();
        if(empty($order)){
            die('订单'.$order_id.'不存在');
        }

        //检查订单状态 是否已支付 已过期 已删除
        if($order->pay_time>0){
            die('该订单已被支付，无法再次支付');
        }

        //调起支付宝支付

        //支付成功 修改支付时间
        $where=[
            'pay_time'=>time(),
            'pay_amount'=>rand(1111,9999),
            'is_pay'=>1
        ];
        OrderModel::where(['order_id'=>$order_id])->update($where);

        //增加消费积分 ..
        header('refresh:2;url=/center');
        echo "支付已成功，正在跳转";
    }
}
