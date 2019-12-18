<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Read;
use Illuminate\Support\Facades\Redis;

class ReadMysqlController extends Controller
{
    //写入缓存
    public function set()
    {

        $data = json_decode(Read::all());
        foreach ($data as $key => $val){
            $id = $data[$key]->id;
            $name = 'user_'.$id;
            Redis::hmset($name,['title'=>$val->title,'stock'=>$val->stock]);
        }
        dd('写入成功');


    }
}