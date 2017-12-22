<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UploadController extends Controller
{
    //
    public function uploadImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'code'=>'ERROR',
                'message'=>"无法获取上传文件！"
            ], 500);
        }
        $file = $request->file('file');
        $name = $file->getClientOriginalName();
        $name = explode('.',$name);
        $allow = \Config::get('fileAllow');
        if (!in_array($name[count($name)-1],$allow)){
            return response()->json([
                'code'=>'ERROR',
                'message'=>'不支持的文件格式'
            ]);
        }
        $md5 = md5_file($file);
        $name = $name[count($name)-1];
        $name = $md5.'.'.$name;
        if (!$file){
            return response()->json([
                'code'=>'ERROR',
                'message'=>'空文件'
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
