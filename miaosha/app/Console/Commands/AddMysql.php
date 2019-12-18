<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class AddMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addmysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '后台redis自动添加到mysql';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        while(1){
//            $data = Redis::rpop('order_num');
//            $arr = \Opis\Closure\unserialize($data);
//            dd($arr['order_number']);
            if (Redis::llen('order_num')>0){
                //弹出数据
                $data = Redis::rpop('order_num');
                //数据转换
                $arr = \Opis\Closure\unserialize($data);
//                dd($arr);
                //数据库操作
                $res =  DB::table('order')->insertGetId(
                    ['order_number' => $arr['order_number'], 'goods_id' => $arr['goods_id'],'user_id' => $arr['user_id'] ]
                );
                echo $res;

//                if (!$order->save()) {
//                    return null;
//                }

            }else{
                sleep(1);
            }
        }
    }
}
