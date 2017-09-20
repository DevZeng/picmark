<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    //
    public function uploadImage(Request $request)
    {
        $file = $request->file('image');
        $name = $file->getClientOriginalName();
        $name = explode('.',$name);
        if (count($name)!=2){
            return response()->json([
                'code'=>'ERROR',
                'msg'=>'非法文件名'
            ]);
        }
        $allow = \Config::get('fileAllow');
        if (!in_array($name[1],$allow)){
            return response()->json([
                'code'=>'ERROR',
                'msg'=>'不支持的文件格式'
            ]);
        }
        $md5 = md5_file($file);
        $name = $name[1];
        $name = $md5.'.'.$name;
        if (!$file){
            return response()->json([
                'code'=>'ERROR',
                'msg'=>'空文件'
            ]);
        }
        if ($file->isValid()){
            $destinationPath = 'uploads';
            $file->move($destinationPath,$name);
            return response()->json([
                'code'=>'OK',
                'base_url'=>$destinationPath.'/'.$name
            ]);
        }
    }
}
