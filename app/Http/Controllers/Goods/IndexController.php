<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\GoodsModel;

class IndexController extends Controller
{
    //


    /**
     * 商品详情
     * @param $goods_id
     */
    public function index($goods_id)
    {
        $goods = GoodsModel::where(['goods_id'=>$goods_id])->first();

        //商品不存在
        if(empty($goods)){
            header('Refresh:2;url=/');
            echo '商品不存在,正在跳转至首页';
            exit;
        }

        $data = [
            'goods' => $goods
        ];
        return view('goods.index',$data);
    }
    //列表展示
    public function list(){
        $list=GoodsModel::all()->toArray();
        $data=[
            'list'=>$list
        ];
        return view('goods.list',$data);
    }
}
