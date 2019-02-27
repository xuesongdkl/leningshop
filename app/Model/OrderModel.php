<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    //

    public $table = 'p_orders';
    public $timestamps = false;


    /**22
     * 生成订单号
     */
    public static function generateOrderSN()
    {
        return 'xs'.date('ymdHis').rand(11111,99999);
    }
}
