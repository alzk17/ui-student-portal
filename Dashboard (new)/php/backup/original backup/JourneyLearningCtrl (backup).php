<?php

namespace App\Http\Controllers\Frontend\Child;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Backend\JourneyLearningMd;
use App\Models\Backend\JourneyLessonMd;
use App\Models\Backend\JourneyPracticeMd;

class JourneyLearningCtrl extends Controller
{

    public function user(){
        return Auth::guard('child');
    }
    public function get(Request $request, $journeyId=null, $subjectId=null)
    {
        $user = self::user();
        $get = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'user' => $user->id
        ])
        ->first();
        if(@$get->id){
            return response()->json($get);
        }else{
            $data = new JourneyLearningMd;
            $data->journeyId = $journeyId;
            $data->subjectId = $subjectId;
            $data->user = $user->id;
            if($data->save()){
                return $data;
            }
        }
    }
    public function setLatest(Request $request, $journeyId=null, $subjectId=null)
    {
        $user = self::user();
        $data = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'user' => $user->id
        ])
        ->first();
        if(@$data->id){
            $data->latest = $request->lessonId;
            if($data->save()){
                $response = [
                    'status' => true,
                    'statusText' => 'The data has been updated.',
                ];
            }else{
                $response = [
                    'status' => false,
                    'statusText' => 'Oops, an error occurred..',
                ];
            }
        }
        return response()->json($response);
    }

    public function getLesson($journeyId = null, $subjectId = null, $lessonId = null)
    {
        try {
            return response()->json([
                'lesson' => JourneyLessonMd::findOrFail($lessonId),
                'practice' => JourneyPracticeMd::where(['subject_id'=>$subjectId,'lesson_id'=>$lessonId])->get()
            ]);
        } catch(\Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
