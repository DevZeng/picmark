<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\PicturePost;
use App\Models\Mark;
use App\Models\Picture;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class PictureController extends Controller
{
    //
    public function addPicture(PicturePost $picturePost)
    {
        $picture = new Picture();
        $picture->url = $picturePost->get('url');
        $picture->user_id = getUserToken($picturePost->get('token'));
        $picture->category = $picturePost->get('category');
        $money = $picturePost->get('money',0);
        if ($money==0){
            $picture->state = 1;
            $picture->save();
            return response()->json([
                'code'=>'OK'
            ]);
        }else{
            $number = self::makePaySn(getUserToken($picturePost->get('token')));
            return response()->json([
                'code'=>'OK',
                'data'=>[
                    'picture_id'=>$picture->id,
                    'number'=>$number
                ]
            ]);
        }
    }

    public function addMark($id)
    {
        $mark = new Mark();
        $mark->pic_id = $id;
        $picture = Picture::find($id);
        if (empty($picture)){
            return response()->json([
                'code'=>'ERROR',
                'message'=>"没找到该图片！"
            ]);
        }
        if ($picture->state==2){
            return response()->json([
                'code'=>'ERROR',
                'message'=>"已点评过的不能再点评!"
            ]);
        }
        $mark = new Mark();
        $mark->pic_id = $id;
        $mark->teacher = getTeacherToken(Input::get('token'));
        $mark->score = Input::get('score');
        $mark->completion = Input::get('completion');
        $mark->concept = Input::get('concept');
        $mark->expression = Input::get('expression');
        $mark->color = Input::get('color');
        $mark->speed = Input::get('speed');
        $mark->detail = Input::get('detail');
        if ($mark->save()) {
            $picture->state = 2;
            $picture->save();
            return response()->json([
                'code'=>'OK'
            ]);
        }
    }
}

