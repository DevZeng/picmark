<?php

namespace App\Http\Controllers\API\V1;

use App\Libraries\Wxxcx;
use App\Models\Mark;
use App\Models\Teacher;
use App\Models\WechatUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
{
    //
    public function OAuthLogin()
    {
        $wxxcx = new Wxxcx(config('wxxcx.app_id'),config('wxxcx.secret'));
        $code = Input::get('code');
        $encryptedData = Input::get('encryptedData');
        $iv = Input::get('iv');
        $sessionKey = $wxxcx->getSessionKey($code);
        $user = $wxxcx->decode($encryptedData,$iv);
        $user = json_decode($user);
        $info = WechatUser::where('open_id','=',$user->openId)->first();
        if(empty($info)){
            $ouser = new WechatUser();
            $ouser->nickname = $user->nickName;
            $ouser->gender = $user->gender;
            $ouser->city = $user->city;
            $ouser->province = $user->province;
            $ouser->avatarUrl = $user->avatarUrl;
            $ouser->open_id = $user->openId;
            if($ouser->save()){
                $key = createNonceStr();
                setUserToken($key,$ouser->id);
                return response()->json([
                    'code'=>'OK',
                    'token'=>$key
                ]);
            }
        }else{
            $key = createNonceStr();
            setUserToken($key,$info->id);
            return response()->json([
                'code'=>'OK',
                'token'=>$key
            ]);
        }
    }
    public function TeacherLogin()
    {
        $code = Input::get('code');
        $teacher = Teacher::where('number','=',$code)->first();
        if (empty($teacher)){
            return response()->json([
                'code'=>'ERROR',
                'message'=>'未找到该教师！'
            ]);
        }else{
            $key = createNonceStr();
            setTeacherToken($key,$teacher->id);
            return response()->json([
                'code'=>'OK',
                'token'=>$key
            ]);
        }
    }
}
