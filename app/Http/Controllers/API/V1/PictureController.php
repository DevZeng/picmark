<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\PicturePost;
use App\Models\ArticlePicture;
use App\Models\Mark;
use App\Models\Picture;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
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
        $picture->price = $money;
        if ($money==0){
            $picture->state = 1;
            $picture->save();
            return response()->json([
                'code'=>'OK'
            ]);
        }else{
            $number = self::makePaySn(getUserToken($picturePost->get('token')));
            $picture->save();
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
            ],422);
        }
        if ($picture->state==2){
            return response()->json([
                'code'=>'ERROR',
                'message'=>"已点评过的不能再点评!"
            ],422);
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
        $mark->pic_url = Input::get('pic_url');
        $mark->issue = Input::get('issue');
        $mark->redo = Input::get('redo');
        if ($mark->save()) {
            $picture->state = 2;
            $picture->teacher_id = getTeacherToken(Input::get('token'));
            $picture->save();
            return response()->json([
                'code'=>'OK'
            ]);
        }
    }
    public function getPictures()
    {
        $type = Input::get('type',1);
        $page = Input::get('page',1);
        $limit = Input::get('limit',10);
        if ($type==1){
            $pictures = Picture::where('user_id','=',getUserToken(Input::get('token')))->where('state','!=','0')->limit($limit)->offset(($page-1)*$limit)->get();
        }elseif ($type==2){
            $category = Teacher::find(getTeacherToken(Input::get('token')))->category;
            $pictures = Picture::where('category','=',$category)->where('state','!=','0')->limit($limit)->offset(($page-1)*$limit)->get();
        }else{
            $pictures = Picture::where('state','!=','0')->limit($limit)->offset(($page-1)*$limit)->get();
        }
        return response()->json([
            'code'=>'OK',
            'data'=>$pictures
        ]);
    }
    public function getPicture($id)
    {
        $picture = Picture::find($id);
        if (empty($picture)){
            return response()->json([
                'code'=>'ERROR',
                'message'=>"没找到该图片！"
            ],404);
        }
        $picture->mark = $picture->mark()->first();
        $teacher = Teacher::find($picture->teacher_id);
        $picture->teacher = empty($teacher)?'':$teacher->name;
        return response()->json([
            'code'=>"OK",
            'data'=>$picture
        ]);
    }
    public function count()
    {
        $time = Input::get('time',date('Y-m-d',time()));
        $date = date('Y-m-01 0:0:0',strtotime($time));
        $end = date('Y-m-d 23:59:59', strtotime("$date +1 month -1 day"));
        $sql = getCountSql($date,$end);
        $count = DB::select($sql);
        $count = $this->formatCount($count);
//        dd($count);
        return response()->json([
            'code'=>'OK',
            'data'=>$count
        ]);
    }
    public function formatCount($count)
    {
        if (empty($count)){
            return [];
        }
        for ($i=0;$i<count($count);$i++){
            $teacher = Teacher::find($count[$i]->teacher_id);
            $count[$i]->teacher = $teacher->name;
            $count[$i]->number = $teacher->number;
        }
        return $count;
    }
    public function addArticle()
    {
        $type = Input::get('type');
        if ($type) {
            $article = ArticlePicture::where('type','=',$type)->first();
            if (empty($article)){
                $article = new ArticlePicture();
            }
            $article->name = Input::get('name');
            $article->url = Input::get('url');
            $article->type = $type;
            if ($article->save()){
                return response()->json([
                    'code'=>'OK'
                ]);
            }
        }else{
            return response()->json([
                'code'=>"ERROR",
                'message'=>"参数错误！"
            ]);
        }
    }
    public function getArticle()
    {
        $article = ArticlePicture::where('type','=',Input::get('type'))->first();
        return response()->json([
            'code'=>'OK',
            'data'=>$article
        ]);
    }
}

