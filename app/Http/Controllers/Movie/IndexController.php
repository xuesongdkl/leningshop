<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/24 0024
 * Time: 15:53
 */

namespace App\Http\Controllers\Movie;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class IndexController extends Controller
{
    public function index(){
        $key='test_bit';
        $seat_status=[];
        for($i=1;$i<=30;$i++){
            $status=Redis::getBit($key,$i);
            var_dump($status);echo "<br>";

            $seat_status[$i]=$status;
        }
        $data=[
            'seat'=>$seat_status
        ];
        echo "<pre>";print_r($seat_status);echo "</pre>";
        return view('movie.index',$data);
    }

    public function buy($pos,$status){
        $key='test_bit';
        Redis::setbit($pos,$status);
    }
}