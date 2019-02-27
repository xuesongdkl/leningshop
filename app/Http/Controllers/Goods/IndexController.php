<?php

namespace App\Http\Controllers\Goods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Model\GoodsModel;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    //


    /**
     * 商品详情
     * @param $goods_id
     */
    public function index($goods_id)
    {

        $goods=GoodsModel::where(['goods_id'=>$goods_id])->first();
        if(!$goods){
            header('Refresh:1;url=/');
            echo "商品不存在，正在跳转到首页";exit;
        }
        $data=[
            'goods'=>$goods
        ];
        return view('goods.index',$data);

    }

    //更新商品信息
    public function updateGoodsInfo($goods_id){
        $name=str_random(6);
        $info=[
            'goods_name'=>$name,
            'add_time'=>time(),
            'price'=>1
        ];
        $goodsinfo=GoodsModel::where(['goods_id'=>$goods_id])->update($info);

    }


    //列表展示
    public function list(){
        $list=GoodsModel::paginate(2);
        $data=[
            'list'=>$list
        ];
        return view('goods.list',$data);
    }

    //文件上传的view
    public function uploadIndex(){
        return view('goods.upload');
    }

    //文件上传
    public function uploadPdf(Request $request){
        $pdf=$request->file('file');
        $rf=$pdf->extension();
        if($rf!='pdf'){
            die('格式不符合PDF格式');
        }
        $res=$pdf->storeAs(date('Ymd'),str_random(4).'.pdf');
        if($res){
            echo "上传成功";
        }
    }

}
