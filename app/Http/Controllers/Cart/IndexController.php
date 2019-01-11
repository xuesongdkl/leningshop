<?php

namespace App\Http\Controllers\Cart;

use App\Model\GoodsModel;
use App\Model\CartModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IndexController extends Controller
{
    public $uid;
    public function __construct()
    {
        $this->middleware(function($request,$next){
            $this->uid=session()->get('uid');
            return $next($request);
        });
    }

    //购物车展示
    public function index(Request $request)
    {
        $cart_goods = CartModel::where(['uid'=>$this->uid])->get()->toArray();
        if(empty($cart_goods)){
            die("购物车是空的");
        }
        if($cart_goods){
            //获取商品最新信息
            foreach($cart_goods as $k=>$v){
                $goods_info = GoodsModel::where(['goods_id'=>$v['goods_id']])->first()->toArray();
                $goods_info['num']  = $v['num'];
                $list[] = $goods_info;
            }
        }

        $data = [
            'list'  => $list
        ];
        return view('cart.index',$data);
    }

    /**
     * 购物车添加商品
     * @return array
     */
    public function add2(Request $request)
    {
        $goods_id = $request->input('goods_id');
        $num = $request->input('num');
        //检查库存
        $store_num = GoodsModel::where(['goods_id'=>$goods_id])->value('store');
        if($store_num<=0){
            $response = [
                'errno' => 5001,
                'msg'   => '库存不足'
            ];
            return $response;
        }

        //写入购物车表
        $data = [
            'goods_id'  => $goods_id,
            'num'       => $num,
            'add_time'  => time(),
            'uid'       => $this->uid,
            'session_token' => session()->get('p_token')
        ];

        $cid = CartModel::insertGetId($data);
        if(!$cid){
            $response = [
                'errno' => 5002,
                'msg'   => '添加购物车失败，请重试'
            ];
            return $response;
        }


        $response = [
            'error' => 0,
            'msg'   => '添加成功'
        ];
        return $response;
    }

    /**
     * 添加商品
     */
    public function add($goods_id)
    {
        $cart_goods=session()->get('cart_goods');

        //是否已在购物车中
        if(!empty($cart_goods)){
            if(in_array($goods_id,$cart_goods)){
                echo "已存在购物车";
                die;
            }
        }
        session()->push('cart_goods',$goods_id);

        //减库存
        $store=GoodsModel::where(['goods_id'=>$goods_id])->value('store');
        if($store<=0){
            echo '库存不足';
            exit;
        }
        $rs=GoodsModel::where(['goods_id'=>$goods_id])->decrement('store');
        if($rs){
            echo "添加成功";
            header("refresh:1;url=/goods/list");
        }
    }

    /**
     * 删除购物车
     */
    public function del($goods_id)
    {
        //判断商品是否在购物车中
        $goods=session()->get('cart_goods');
//        echo "<pre>";print_r($goods);echo "</pre>";
        if(in_array($goods_id,$goods)){
            //删除
            foreach($goods as $k=>$v){
                if($goods_id==$v){
                    session()->pull('cart_goods.'.$k);
                }
            }
            echo "删除成功";
        }else{
            //不在购物车中
            echo "商品不在购物车中";
            die;
        }
    }


    /*
     * 删除商品
     */
    public function del2(Request $request){
        $goods_id=$request->input('goods_id');
        $rs=CartModel::where(['uid'=>$this->uid,'goods_id'=>$goods_id])->delete();
        $response= [
            'error'=>0,
            'msg'=>'删除购物车成功'
        ];
        return $response;
    }


}
