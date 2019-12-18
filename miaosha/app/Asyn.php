<?php
namespace App;

use Illuminate\Support\Facades\Redis;
use App\Models\Order;

while(1){
    if (Redis::llen('order_num')>0){
        //弹出数据
        $data = Redis::rpop('order_num');
        //数据转换
        $arr = \Opis\Closure\unserialize($data);
        //数据库操作
        $order = new Order();
        $order->order_number = $arr['order_number'];
        $order->goods_id = $arr['goods_id'];
        $order->user_id = $arr['user_id'];
        if (!$order->save()) {
            return null;
        }

    }else{
        sleep(1);
    }
}
