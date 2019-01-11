<?php

namespace App\Http\Controllers\Order;

use App\Model\CartModel;
use App\Model\GoodsModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\OrderModel;

class IndexController extends Controller
{
    //

    public function index()
    {
        echo __METHOD__;
    }

    /**
     * 下单
     */
    public function add(Request $request)
    {
        //查询购物车商品
        $cart_goods = CartModel::where(['uid'=>session()->get('uid')])->orderBy('id','desc')->get()->toArray();

        if(empty($cart_goods)){
            die("购物车中无商品");
        }
        $order_amount = 0;

        foreach($cart_goods as $k=>$v){
            $goods_info = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
            $goods_info['num'] = $v['num'];
            $list[] = $goods_info;

            //计算订单价格 = 商品数量 * 单价
            $order_amount += $goods_info['price'] * $v['num'];
        }

        //生成订单号
        $order_sn = OrderModel::generateOrderSN();
        echo $order_sn;echo "</br>";
        $data = [
            'order_sn'      => $order_sn,
            'uid'           => session()->get('uid'),
            'add_time'      => time(),
            'order_amount'  => $order_amount
        ];
        $oid=OrderModel::insertGetId($data);
//        dump($oid);die;
        if(empty($oid)){
            echo '生成订单失败';
        }



        //清空购物车
        CartModel::where(['uid'=>session()->get('uid')])->delete();
        echo '下单成功,订单号：'.$oid .' 跳转支付';
        header('refresh:1;url="/order/list"');

    }

    /*
     * 订单列表展示
     * */
    public function list(){
        $list=OrderModel::all()->toArray();
        $data=[
          'list'=>$list
        ];
        return view('order.index',$data);
    }


}
