<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\OrderPost;
use App\Libraries\WxPay;
use App\Models\Order;
use App\Models\Picture;
use App\Models\WechatUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class OrderController extends Controller
{
    //
    public function makeOrder(OrderPost $orderPost)
    {
        $order = new Order();
        $order->number = $orderPost->get('number');
        $order->pic_id = $orderPost->get('pic_id');
        $picture = Picture::find($orderPost->get('pic_id'));
        $order->price = $picture->price;
        $user = WechatUser::find(getUserToken(Input::get('token')));
        $payment = new WxPay(config('wxxcx.app_id'),config('wxxcx.mch_id'),config('wxxcx.api_key'),$user->open_id);
        if ($order->save()){
            return response()->json([
                'code'=>'OK',
                'data'=>$payment->pay($order->number,'打赏',($order->price*100))
            ]);
        }
    }
    public function getOrders()
    {
        $limit = Input::get('limit',10);
        $page = Input::get('page',1);
        $state = Input::get('state',1);
        $orders = Order::where(['state'=>$state])->limit($limit)->offset(($page-1)*$limit)->get();
        return response()->json([
            'code'=>'OK',
            'data'=>$orders
        ]);
    }
    public function notify(Request $request)
    {
        $data = $request->getContent();
        $wx = WxPay::xmlToArray($data);
        $wspay = new WxPay(config('wxxcx.app_id'),config('wxxcx.mch_id'),config('wxxcx.api_key'),$wx['openid']);
        $data = [
            'appid'=>$wx['appid'],
            'cash_fee'=>$wx['cash_fee'],
            'bank_type'=>$wx['bank_type'],
            'fee_type'=>$wx['fee_type'],
            'is_subscribe'=>$wx['is_subscribe'],
            'mch_id'=>$wx['mch_id'],
            'nonce_str'=>$wx['nonce_str'],
            'openid'=>$wx['openid'],
            'out_trade_no'=>$wx['out_trade_no'],
            'result_code'=>$wx['result_code'],
            'return_code'=>$wx['return_code'],
            'time_end'=>$wx['time_end'],
            'total_fee'=>$wx['total_fee'],
            'trade_type'=>$wx['trade_type'],
            'transaction_id'=>$wx['transaction_id']
        ];
        $sign = $wspay->getSign($data);
        if ($sign == $wx['sign']){
            $order = Order::where(['number'=>$wx['out_trade_no']])->first();
            if ($order->state==0){
                $picture = Picture::find($order->pic_id);
                $picture->state = 1;
                $picture->save();
                $order->state = 1;
                $order->transaction_id = $wx['transaction_id'];
                if ($order->save()){
                    return 'SUCCESS';
                }
            }

        }
        return 'ERROR';
    }
}
