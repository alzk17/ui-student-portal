<?php

namespace App\Http\Controllers\Frontend\Child;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Backend\JourneyAnswerMd;
use App\Models\Backend\JourneyTestAnswerMd;
use App\Models\Backend\JourneyMd;
use App\Models\Backend\JourneySubjectMd;
use App\Models\Backend\JourneyLessonMd;
use App\Models\Backend\JourneyLearningMd;
use App\Models\Backend\JourneyLessonCompletionMd;
use App\Models\Backend\JourneyPracticeMd;
use App\Models\Backend\PracticeModel;
use Illuminate\Support\Str;

class JourneyCtrl extends Controller
{
    protected $prefix = 'front-end';
    protected $folder = 'dashboard-child';

    public function index(Request $request, $journeyId = null, $subjectId = null)
    {
        $child = Auth::guard('child')->user();
        $navs = [
            ["url" => url('/dashboard-child/journey'), "name" => "Learning Journey"]
        ];

        $subject = \App\Models\Backend\JourneySubjectMd::with('journey')
            ->where('id', $subjectId)
            ->where('journey_id', $journeyId)
            ->firstOrFail();

        $lessons = \App\Models\Backend\JourneyLessonMd::where('journey_subject_id', $subjectId)
            ->orderBy('list_order', 'asc')
            ->get();

        $learningProgress = \App\Models\Backend\JourneyLearningMd::firstOrCreate([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId'    => $child->id
        ]);

        $lessonIdsInThisTopic = $lessons->pluck('id');
        $completedLessonIds = \App\Models\Backend\JourneyLessonCompletionMd::where('user_id', $child->id)
            ->whereIn('lesson_id', $lessonIdsInThisTopic)
            ->pluck('lesson_id')
            ->toArray();

        $firstIncomplete = $lessons->first(function ($lesson) use ($completedLessonIds) {
            return !in_array($lesson->id, $completedLessonIds, true);
        });

        // Always derive current from the first incomplete lesson (ignoring any stale flags)
        $currentLessonId = $firstIncomplete->id ?? null;

        $totalLessons = $lessons->count();
        $completedCount = count($completedLessonIds);
        $topicProgressPercent = ($totalLessons > 0)
            ? round(($completedCount / $totalLessons) * 100)
            : 0;

        // Build a continue URL only when there is something left to learn
        $continueUrl = null;
        if ($currentLessonId && $topicProgressPercent < 100) {
            $continueUrl = url('dashboard-child/journey/' . $subject->journey->id . '/' . $subject->id . '/learning?lesson=' . $currentLessonId);
        }

        return view("front-end.dashboard-child.subject-overview", [
            'prefix'             => $this->prefix,
            'folder'             => $this->folder,
            'nav'                => $navs,
            'subject'            => $subject,
            'lessons'            => $lessons,
            'journey'            => $subject->journey,
            'user'               => Auth::guard('user'),
            'child'              => $child,
            'completedLessonIds' => $completedLessonIds,
            'currentLessonId'    => $currentLessonId,
            'topicProgressPercent' => $topicProgressPercent,
            'continueUrl'        => $continueUrl, // Pass the new URL to the view
        ]);
    }

    public function getLessons(Request $request, $journeyId = null, $subjectId = null)
    {
        try {
            $user = Auth::guard('child')->user();
            
            // UPDATED: This now explicitly selects all the columns we need, including 'type'
            $data = JourneyLessonMd::select('id', 'name', 'type', 'list_order')
                ->where(['journey_id' => $journeyId, 'journey_subject_id' => $subjectId])
                ->orderBy('list_order', 'asc')
                ->get();

            $learningQuery = JourneyLearningMd::where(['subjectId' => $subjectId, 'journeyId' => $journeyId, 'userId' => $user->id])->first();

            $current = JourneySubjectMd::where(['journey_id' => $journeyId, 'id' => $subjectId])
                ->select('id', 'name', 'timer', 'list_order')
                ->first();

            $next = null;
            if ($current) {
                $next = JourneySubjectMd::where([
                    'journey_id' => $journeyId,
                    'list_order' => $current->list_order + 1
                ])->select('id')->first();
            }
            
            if (!@$learningQuery->id) {
                $learning = new JourneyLearningMd;
                $learning->journeyId = $journeyId;
                $learning->subjectId = $subjectId;
                $learning->userId = $user->id;
                $learning->save();
            } else {
                $learning = $learningQuery;
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
        $subject = \App\Models\Backend\JourneySubjectMd::with('journey')
            ->where('id', $subjectId)
            ->firstOrFail();

        // --- THE FIX ---
        // 1. Get the lesson ID that the user clicked on from the request URL.
        $clickedLessonId = $request->query('lesson');

        // 2. Validate that this lesson actually belongs to the topic.
        $lesson = \App\Models\Backend\JourneyLessonMd::where('id', $clickedLessonId)
            ->where('journey_subject_id', $subjectId)
            ->first();

        // 3. If the clicked lesson is not valid, redirect back to the map.
        //    This is a security measure to prevent users from accessing lessons from other topics.
        if (!$lesson) {
            return redirect('dashboard-child/journey/' . $journeyId . '/' . $subjectId);
        }
        // --- END OF FIX ---

        return view("front-end.dashboard-child.learning-journey", [
            'prefix'    => 'front-end',
            'folder'    => $this->folder,
            'nav'       => $navs,
            'journey'   => $subject->journey,
            'subject'   => $subject,
            'user'      => Auth::guard('user'),
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'lessonId'  => $lesson->id, // Pass the CORRECT lesson ID to the view
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
                $all = ($data->all_latest) ? json_decode($data->all_latest, true) : [];
                if (!is_array($all)) { $all = []; }
                $lessonId = (int) $request->lessonId;
                if ($lessonId && !in_array($lessonId, $all, true)) {
                    $all[] = $lessonId;
                }

                $data->latest_type = $request->latest_type;
                $data->all_latest = json_encode($all);
                $data->latest = $lessonId;

                if ($data->save()) {
                    $res = [
                        'status' => true,
                        'statusText' => 'Data has been updated.',
                        'statusCode' => 200,
                        'latest' => $lessonId,
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
                $userAnswer = JourneyAnswerMd::where([
                    'practiceId' => $v->id,
                    'subjectId'  => $subjectId,
                    'userId'     => Auth::guard('child')->user()->id
                ])->first();
                if (!$userAnswer) { continue; }
                $testCorrect = '';
                if($v->type_question == 'drag-drop'){
                    $testAnswer = JourneyTestAnswerMd::Where(['practice_id'=>$v->id])->orderBy('list_answer')->get();
                    $array = [];
                    foreach($testAnswer as $ans) {
                        $array[] = $ans->answer_text;
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
                if($userAnswer->answer_text == $testCorrect){
                    $correct++;
                }
            }
        } catch(\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    function finishedLearning(Request $request, $journeyId = null, $subjectId = null)
    {
        $child = Auth::guard('child')->user();
        $learningProgress = \App\Models\Backend\JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId'    => $child->id
        ])->first();

        if ($learningProgress) {
            \App\Models\Backend\JourneyLessonCompletionMd::firstOrCreate([
                'user_id'   => $child->id,
                'lesson_id' => $request->lessonId,
            ]);

            $currentLesson = \App\Models\Backend\JourneyLessonMd::find($request->lessonId);
            $nextLesson = null;
            if ($currentLesson) {
                $nextLesson = \App\Models\Backend\JourneyLessonMd::where('journey_id', $journeyId)
                    ->where('journey_subject_id', $subjectId)
                    ->where('list_order', '>', $currentLesson->list_order)
                    ->orderBy('list_order', 'asc')
                    ->first();
            }

            $learningProgress->latest = $nextLesson ? $nextLesson->id : null;

            // --- THE FIX ---
            // If there is no next lesson, mark the entire topic as finished.
            if (!$nextLesson) {
                $learningProgress->finished = 1; // Mark as complete
            }
            // --- END OF FIX ---
            
            $learningProgress->save();

            $res = [
                'status'       => true,
                'statusCode'   => 200,
                'message'      => 'Lesson completion recorded.',
                'nextLessonId' => $nextLesson ? $nextLesson->id : null,
            ];
        } else {
            $res = ['status' => false, 'statusCode' => 404, 'message' => 'Learning progress record not found.'];
        }

        return response()->json($res);
    }

    public function learningReset(Request $request, $journeyId = null, $subjectId = null)
    {
        $childId = Auth::guard('child')->id();

        // Find the student's "bookmark" for this topic
        $learning = \App\Models\Backend\JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId'    => $childId
        ])->first();

        if ($learning) {
            // Find all lesson IDs in this topic to clear completions
            $lessonIds = \App\Models\Backend\JourneyLessonMd::where('journey_subject_id', $subjectId)->pluck('id');

            // 1. Clear the "checklist" of completed lessons for this topic
            \App\Models\Backend\JourneyLessonCompletionMd::where('user_id', $childId)
                ->whereIn('lesson_id', $lessonIds)
                ->delete();

            // 2. Clear the "bookmark" by resetting the main progress record
            $learning->latest = null;
            $learning->save();
            
            $response = ['status' => 200, 'message' => 'Progress has been reset.'];
        } else {
            $response = ['status' => 404, 'message' => 'Learning progress not found.'];
        }
        
        return response()->json($response);
    }
}