<?php

namespace App\Http\Controllers\Frontend\Child;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\LessonsResource;
use App\Models\Backend\JourneyAnswerMd;
use App\Models\Backend\JourneyTestAnswerMd;
use App\Models\Backend\JourneyMd;
use App\Models\Backend\JourneySubjectMd;
use App\Models\Backend\JourneyLessonMd;
use App\Models\Backend\JourneyLearningMd;
use App\Models\Backend\JourneyPracticeMd;
use App\Models\Backend\PracticeModel;
use Svg\Tag\Rect;

class JourneyCtrl extends Controller
{
    protected $prefix = 'front-end';
    protected $folder = 'dashboard-child';
    
    public function index(Request $reqest, $journeyId = null, $subjectId = null)
    {
        $navs = [
            ["url" => url('/dashboard-child/journey'), "name" => "Learning Journey"]
        ];

        $subject = JourneySubjectMd::leftJoin('tb_journey as jn', 'tb_journey_subject.journey_id', 'jn.id')
            ->where('tb_journey_subject.journey_id', $journeyId)
            ->where('tb_journey_subject.id', $subjectId)
            ->select([
                'tb_journey_subject.*',
                'jn.name as journey'
            ])
            ->first();

        return view("front-end.dashboard-child.subject-overview", [
            'prefix' => 'front-end',
            'folder' => $this->folder,
            'nav' => $navs,
            'subject' => $subject,
            'user' => Auth::guard('user'),
			'child' => Auth::guard('child')->user() // ✅ Add this line (to temporary fix accessing journey page)

        ]);
    }

    public function getLessons(Request $reqest, $journeyId = null, $subjectId = null)
    {
        try {
            $user = Auth::guard('child')->user();
            $lessons = new JourneyLessonMd;
            $data = ($subjectId) ? $lessons->where(['journey_id' => $journeyId, 'journey_subject_id' => $subjectId])->get()->toArray() : [];
            $learingQuery = JourneyLearningMd::where(['subjectId' => $subjectId, 'journeyId' => $journeyId, 'userId' => $user->id])->first();
            $current = JourneySubjectMd::where(['journey_id' => $journeyId, 'id' => $subjectId])->select('id', 'name', 'timer')->first();
            $next = JourneySubjectMd::where(['journey_id' => $journeyId, 'list_order' => $current->list_order + 1])->select('id')->first();
            if (@$learingQuery->id) {
                $learning = $learingQuery;
            } else {
                $learning = new JourneyLearningMd;
                $learning->journeyId = $journeyId;
                $learning->subjectId = $subjectId;
                $learning->userId = $user->id;
                $learning->save();
            }
            return response()->json([
                'subject' => $current,
                'learning' => $learning,
                'next' => $next,
                'lessons' => LessonsResource::collection($data)
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function learning(Request $reqest, $journeyId = null, $subjectId = null)
    {
        $navs = [
            ["url" => url('/dashboard-child/journey'), "name" => "Learning Journey"]
        ];
        $subject = JourneySubjectMd::leftJoin('tb_journey as journey', 'tb_journey_subject.journey_id', 'journey.id')
            ->where('tb_journey_subject.id', $subjectId)
            ->select([
                'tb_journey_subject.*',
                'journey.id as journeyId',
                'journey.name as journey'
            ])
            ->first();


        $lessons = JourneySubjectMd::leftJoin('tb_journey as journey', 'tb_journey_subject.journey_id', 'journey.id')
            ->leftJoin('tb_journey_subject_lessons as lesson', 'tb_journey_subject.id', 'lesson.journey_subject_id')
            ->where('tb_journey_subject.id', $subjectId)
            ->select([
                'tb_journey_subject.*',
                'journey.id as journeyId',
                'journey.name as journeyName',
                'lesson.id as lessonId',
                'lesson.name as lessonName',
                'lesson.content as lessonContent',
            ])
            ->get();

        $latest = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId' => Auth::guard('child')->user()->id
        ])
            ->first();

        $learning = [];
        foreach ($lessons as $k => $v) {
            $learning[] = (object)[
                'lessonId' => $v->lessonId,
                'journeyId' => $v->journeyId,
                'subjectId' => $v->id,
                'lessonName'  => $v->lessonName,
            ];
        }

        if (@$latest->id && @$latest->learning == NULL) {
            if (count($learning) > 0) {
                $latest->learning = json_encode($learning);
                $latest->save();
            }
        }
        if (@$latest->id && $latest->latest == NULL) {
            $latest->all_latest = '["' . $learning[0]->lessonId . '"]';
            $latest->latest = $learning[0]->lessonId;
            $latest->latest_type = 'lesson';
            $latest->save();
        }


        return view("front-end.dashboard-child.learning-journey", [
            'prefix' => 'front-end',
            'folder' => $this->folder,
            'nav' => $navs,
            'journey' => \App\Models\Backend\JourneyMd::findOrFail($journeyId),
            'subject' => $subject,
            'user' => Auth::guard('user'),
            'latest' => $latest
        ]);
    }


    public function setLatest(Request $request, $journeyId = null, $subjectId = null)
    {
        try {
            $data = JourneyLearningMd::where([
                'journeyId' => $journeyId,
                'subjectId' => $subjectId,
                'userId' => Auth::guard('child')->user()->id
            ])->first();

            if (@$data->id) {
                $all = ($data->all_latest) ? json_decode($data->all_latest) : [];
                array_push($all, $request->latest);
                $data->latest_type = $request->latest_type;
                $data->all_latest = json_encode($all);
                $data->latest = $request->latest;
                if ($data->save()) {
                    $res = ['status' => true, 'statusText' => 'Data has been updated.', 'statusCode' => 200, 'latest' => $request->latest, 'latest_type' => $request->latest_type];
                } else {
                    $res = ['status' => false, 'statusText' => 'Not Modified.', 'statusCode' => 304];
                }
            } else {
                $res = ['status' => false, 'statusText' => 'No Content.', 'statusCode' => 204];
            }
            return response()->json($res);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function sendAnswer(Request $request, $journeyId = null, $subjectId = null)
    {
        try {

            $data = \App\Models\Backend\JourneyAnswerMd::where(['subjectId' => $subjectId, 'practiceId' => $request->practiceId])->first();
            if (!@$data->id) {
                $data = new \App\Models\Backend\JourneyAnswerMd;
            }
            $data->subjectId = $subjectId;
            $data->practiceId = $request->practiceId;
            $data->userId = Auth::guard('child')->user()->id;
            if (@$request->testId) $data->testId = json_encode($request->testId);
            $data->userId = Auth::guard('child')->user()->id;
            $data->answer_type = $request->answer_type;
            if (@$request->answer) $data->answer = json_encode($request->answer);
            if (@$request->answer_text) {
                $data->answer_text = json_encode($request->answer_text);
            }
            if ($data->save()) {
                $res = ['status' => 201, 'statusText' => 'Data has been stored.'];
            } else {
                $res = ['status' => 500, 'statusText' => 'An error occurred.'];
            }
            return response()->json($res);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function getExamination($journeyId = null, $subjectId = null)
    {
        try {
            $correct = 0;
            $practice = PracticeModel::where([
                'journey_id' => $journeyId,
                'subject_id' => $subjectId
            ])->get();
            foreach($practice as $k => $v){
                $answer = JourneyAnswerMd::where([
                    'practice_id'=>$v->id,
                    'subject_id',
                    'userId'=>Auth::guard('child')->user()->id
                ])->first();
                $testCorrect = '';
                if($v->type_question == 'drag-drop'){
                    $testAnswer = JourneyTestAnswerMd::Where(['practice_id'=>$v->id])->orderBy('list_answer')->get();
                    $array = [];
                    foreach($testAnswer as $answer) {
                        $array[] = $answer->answer_text;
                    }
                    $testCorrect = json_encode($array);
                    $testCorrect = str_replace($testCorrect,"{","[");
                    $testCorrect = str_replace($testCorrect,"}","]");
                }else{
                    $testAnswer = JourneyTestAnswerMd::Where(['practice_id'=>$v->id,'correct_status'=>'1'])->first();
                    if(@$testAnswer->id){
                        $testCorrect = '["'.$testAnswer->answer_text.'"]';
                    }
                }
                if($answer->answer_text == $testCorrect){
                    $correct++;
                }
            }
        } catch(\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    function finishedLearning(Request $request, $journeyId = null, $subjectId = null)
    {
        $data  = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId' => Auth::guard('child')->user()->id
        ])->first();
        if ($data->id) {
            $data->finished = true;
            $data->timer = $request->timer;
            if ($data->save()) {
                $child = Auth::guard('child')->user();
                // $streak_point = 
                $streak_point = $child->streak_point;
                $streak_point = $streak_point + 1;
                $child->streak_point = $streak_point;
                $child->save();

                $res = ['status' => false, 'statusCode' => 200, 'message' => 'Data has been saved.'];
            } else {
                $res = ['status' => false, 'statusCode' => 500, 'message' => 'An error occurred.'];
            }
        } else {
            $res = ['status' => false, 'statusCode' => 500, 'message' => 'Data not found.'];
        }
        return response()->json($res);
    }

    public function learningReset(Request $request, $journeyId = null, $subjectId = null)
    {
        $learning = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId' => Auth::guard('child')->user()->id
        ])->first();
        $subject = JourneySubjectMd::findOrFail($subjectId);
        if ($learning->id) {
            $lesson = JourneyLessonMd::where('journey_subject_id', $subject->id)->get();
            foreach ($lesson as $v) {
                foreach (
                    JourneyPracticeMd::where([
                        'subject_id' => $v->journey_subject_id,
                        'lesson_id' => $v->id
                    ])->get() as $k => $p
                ) {
                    $delete[$k] = JourneyAnswerMd::where([
                        'subjectId' => $subjectId,
                        'practiceId' => $p->id,
                        'userId' => Auth::guard('child')->user()->id
                    ])->delete();
                }
            }
            $learning->learning = NULL;
            $learning->all_latest = NULL;
            $learning->latest = NULL;
            $learning->latest_type = NULL;
            $learning->finished = 0;
            $learning->timer = NULL;
            $learning->save();
            if (@in_array(true, $delete) >= 0) {
                $response = ['status' => 200, 'statusText' => 'Data has been deleted.'];
            } else {
                $response = ['status' => 500, 'statusText' => 'Your request was unsuccessful.'];
            }
        } else {
            $response = ['status' => 500, 'statusText' => 'Learning not found.'];
        }
        return response()->json($response);
    }
}
