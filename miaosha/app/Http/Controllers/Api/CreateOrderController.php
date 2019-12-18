<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class CreateOrderController extends Controller
{

    public function purchase(Request $request, $id)
    {
        //数据效验
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|string|min:2|max:30',
            'user_password' => 'required|string|min:1|max:18',

        ],
            [
                'required' => ':attribute为必填项',
                'in' => ':attribute类型错误',
                'min' => ':attribute长度不符合要求',
                'max' => ':attribute长度超过限制',
            ],
            [
                'user_password' => '密码',
                'user_name' => '名称',

            ]
        );

//        if ($validator->fails()) {
//            return response()->json([
//                'code' => -1,
//                'msg' => $validator->errors()->first(),
//                'data' => []
//            ]);
//        }

        //$id是商品id  验证是否还有库存
        $redis_key = 'user_' . $id;
        $len = Redis::hget($redis_key, 'stock');
        if ($len == 0) {
            dd('抢光了');
        }

        //通过验证
        $res = Redis::hincrby($redis_key, 'stock', -1);
        if ($res < 0) {
            return response()->json([
                'code' => -1,
                'msg' => '购买失败',
                'data' => []
            ]);
        }

        $userId = rand(1,1000);
        $order_number = date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
//        dd($order_number);

        $orderkey = 'order_num';
        $arr=['order_number'=>$order_number,'goods_id'=>$id,'user_id'=>$userId];
        $list = serialize($arr);
       $res=Redis::lpush($orderkey,$list);
        if ($res){
            return response()->json([
                'code' => 0,
                'msg' => '购买成功',
                'data' => [
                    'order_number'=>$order_number
                ]
            ]);
        }



    }


}