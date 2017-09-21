<?php

namespace App\Http\Controllers\API\V1;

use App\Libraries\WxPay;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class OrderController extends Controller
{
    //
    public function makeOrder()
    {
        $order = new Order();
        $order->number = Input::get('number');
        $order->price = Input::get('price');
        $order->pic_id = Input::get('pic_id');
        $payment = new WxPay();
    }
    public function getOrders()
    {
        $limit = Input::get('limit',10);
        $page = Input::get('page',1);
        $state = Input::get('state',1);
        $orders = Order::where(['state'=>$state])->limit($limit)->offset(($page-1)*$limit);
        return response()->json([
            'code'=>'OK',
            'data'=>$orders
        ]);
    }

}
