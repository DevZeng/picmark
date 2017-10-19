<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\TeacherPost;
use App\Libraries\Wxxcx;
use App\Models\Count;
use App\Models\Mark;
use App\Models\Picture;
use App\Models\SConfig;
use App\Models\Teacher;
use App\Models\WechatUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        $teacher = Teacher::where('number','=',$code)->where('state','=',1)->first();
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
    public function addTeacher(TeacherPost $teacherPost)
    {
        $teacher = new Teacher();
        $teacher->name = $teacherPost->get('name');
        $teacher->number = $teacherPost->get('number');
        $teacher->category = $teacherPost->get('category');
        $teacher->description = $teacherPost->get('description');
        if ($teacher->save()){
            return response()->json([
                'code'=>'OK'
            ]);
        }
    }
    public function getTeachers()
    {
        $limit = Input::get('limit',10);
        $page = Input::get('page',1);
        $teachers = Teacher::where('state','!=',0)->limit($limit)->offset(($page-1)*$limit)->get();
        return response()->json([
            'code'=>'OK',
            'data'=>$teachers
        ]);
    }

    public function delTeacher($id)
    {
        $teacher = Teacher::find($id);
        $teacher->state = 0;
        if ($teacher->save()){
            return response()->json([
                'code'=>'OK'
            ]);
        }
    }
    public function login()
    {
        $username = Input::get('username');
        $password = Input::get('password');
        if (Auth::attempt(['name'=>$username,'password'=>$password],true)){
            return response()->json([
                'code'=>"OK"
            ]);
        }else{
            return response()->json([
                'code'=>"ERROR",
                'message'=>'账户不存在或密码错误！'
            ],422);
        }
    }

    public function count()
    {
        $time = Input::get('time',date('Y-m-d',time()));
        $date = date('Y-m-01 0:0:0',strtotime($time));
        $end = date('Y-m-d 23:59:59', strtotime("$date +1 month -1 day"));
        $id = getTeacherToken(Input::get('token'));
        $count = Count::where('teacher_id','=',$id)->whereBetween('created_at', [$date,$end ])->sum('price');
        $teacher = Teacher::find($id);
        $category = empty($teacher)?0:$teacher->category;
        return response()->json([
            'code'=>'OK',
            'data'=>[
                'count'=>$count,
                'category'=>$category
            ]
        ]);
    }
    public function modifyConfig()
    {
        $config = SConfig::first();
        if (empty($config)){
            $config = new SConfig();
        }
        $price = Input::get('price',1);
        $config->price = $price;
        if ($config->save()){
            return response()->json([
                'code'=>'OK'
            ]);
        }
    }
    public function getConfig()
    {
        $config = SConfig::first();
        if (empty($config)){
            $config = new SConfig();
        }
        return response()->json([
            'code'=>'OK',
            'data'=>$config
        ]);
    }
    public function editTeacher($id)
    {
        $teacher = Teacher::find($id);
        if (empty($id)){
            return response()->json([
                'code'=>'ERROR',
                'message'=>'该教师不存在！'
            ],422);
        }
        $name = Input::get('name');
        $desc = Input::get('description');
        $code = Input::get('code');
        $teacher->name = empty($name)?$teacher->name:$name;
        $teacher->description = empty($desc)?$teacher->description:$desc;
        $teacher->number = empty($code)?$teacher->number:$code;
        if ($teacher->save()){
            return response()->json([
                'code'=>'OK'
            ]);
        }
    }
    public function teacherCount()
    {
        $count = Teacher::where('state','!=',0)->count();
        return response()->json([
            'code'=>'OK',
            'data'=>$count
        ]);
    }
}
