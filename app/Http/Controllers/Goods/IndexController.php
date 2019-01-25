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
        $redis_goods_key="h_goods_info_".$goods_id;
        echo $redis_goods_key;
        $goods_info=Redis::hGetAll($redis_goods_key);
        if($goods_info){
            echo "Redis";
            echo "<pre>";print_r($goods_info);echo "</pre>";
        }else{
            echo "Mysql";
            $goods = GoodsModel::where(['goods_id'=>$goods_id])->first()->toArray();
            echo "<pre>";print_r($goods);echo "</pre>";
            //写入缓存
            $rs=Redis::hmset($redis_goods_key,$goods);
            //设置缓存过期时间
            Redis::expire($redis_goods_key,10);
        }

        die;
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

    //更新商品信息
    public function updateGoodsInfo($goods_id){
        $name=str_random(6);
        $info=[
            'goods_name'=>$name,
            'add_time'=>time(),
            'price'=>rand(111,999)
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
