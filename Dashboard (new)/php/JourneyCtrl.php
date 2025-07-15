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
use App\Models\Backend\JourneyLessonCompletionMd;
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
            
            // UPDATED: This now explicitly selects all the columns we need, including 'type'
            $data = JourneyLessonMd::select('id', 'name', 'type', 'list_order')
                ->where(['journey_id' => $journeyId, 'journey_subject_id' => $subjectId])
                ->orderBy('list_order', 'asc')
                ->get();

            $learingQuery = JourneyLearningMd::where(['subjectId' => $subjectId, 'journeyId' => $journeyId, 'userId' => $user->id])->first();
            $current = JourneySubjectMd::where(['journey_id' => $journeyId, 'id' => $subjectId])->select('id', 'name', 'timer')->first();
            $next = JourneySubjectMd::where(['journey_id' => $journeyId, 'list_order' => $current->list_order + 1])->select('id')->first();
            
            if (!@$learingQuery->id) {
                $learning = new JourneyLearningMd;
                $learning->journeyId = $journeyId;
                $learning->subjectId = $subjectId;
                $learning->userId = $user->id;
                $learning->save();
            } else {
                $learning = $learingQuery;
            }

            return response()->json([
                'subject' => $current,
                'learning' => $learning,
                'next' => $next,
                'lessons' => $data // Directly use the collection
            ]);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function learning(Request $request, $journeyId = null, $subjectId = null)
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

        // Only fetch lessons for this journey AND this subject, ordered!
        $lessons = JourneyLessonMd::where('journey_id', $journeyId)
            ->where('journey_subject_id', $subjectId)
            ->orderBy('list_order', 'asc')
            ->get();

        $latest = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId' => Auth::guard('child')->user()->id
        ])->first();

        // Always start with the lowest list_order lesson unless user progress exists
        $firstLesson = $lessons->first();
        $lessonId = $latest->latest ?? ($firstLesson ? $firstLesson->id : null);

        return view("front-end.dashboard-child.learning-journey", [
            'prefix' => 'front-end',
            'folder' => $this->folder,
            'nav' => $navs,
            'journey' => \App\Models\Backend\JourneyMd::findOrFail($journeyId),
            'subject' => $subject,
            'user' => Auth::guard('user'),
            'latest' => $latest,
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'lessonId' => $lessonId
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
                
                // CORRECTED: Use lessonId here
                array_push($all, $request->lessonId); 

                $data->latest_type = $request->latest_type;
                $data->all_latest = json_encode($all);

                // This is the main fix you already made
                $data->latest = $request->lessonId; 

                if ($data->save()) {
                    $res = [
                        'status' => true, 
                        'statusText' => 'Data has been updated.', 
                        'statusCode' => 200, 
                        // CORRECTED: Use lessonId here as well for the response
                        'latest' => $request->lessonId, 
                        'latest_type' => $request->latest_type
                    ];
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
        // Find the user's main progress record for this topic
        $learningProgress = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId' => Auth::guard('child')->user()->id
        ])->first();

        if ($learningProgress) {
            $learningProgress->timer = $request->timer;
            $learningProgress->save();

            // Update user's streak points
            $child = Auth::guard('child')->user();
            $child->streak_point = $child->streak_point + 1;
            $child->save();

            // Record the specific lesson completion in our new table.
            \App\Models\Backend\JourneyLessonCompletionMd::create([
                'user_id' => $child->id,
                'lesson_id' => $request->lessonId,
            ]);

            // Find the ID of the next lesson in the sequence
            $currentLesson = \App\Models\Backend\JourneyLessonMd::find($request->lessonId);
            $nextLessonId = null;

            if ($currentLesson) {
                $nextLesson = \App\Models\Backend\JourneyLessonMd::where('journey_id', $journeyId)
                    ->where('journey_subject_id', $subjectId)
                    ->where('list_order', '>', $currentLesson->list_order)
                    ->orderBy('list_order', 'asc')
                    ->first();

                if ($nextLesson) {
                    $nextLessonId = $nextLesson->id;
                }
            }

            // Return a success response with the next lesson's ID
            $res = [
                'status' => true,
                'statusCode' => 200,
                'message' => 'Lesson completion recorded.',
                'nextLessonId' => $nextLessonId
            ];

        } else {
            $res = ['status' => false, 'statusCode' => 500, 'message' => 'Learning progress record not found.'];
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