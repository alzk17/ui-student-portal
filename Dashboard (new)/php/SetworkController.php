<?php

namespace App\Http\Controllers\Frontend\Child;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Backend\LogsController;
use App\Helpers\Helper;
use App\Http\Controllers\Functions\FunctionControl;
use App\Models\Backend\Child_mocktest_question_topicModel;
use Intervention\Image\ImageManagerStatic as Image;

use App\Models\Backend\UserModel;
use App\Models\Backend\ChildModel;
use App\Models\Backend\User_packageModel;
use App\Models\Backend\Package_subjectModel;
use App\Models\Backend\SubjectModel;

use App\Models\Backend\QuestionModel;
use App\Models\Backend\PracticeModel;
use App\Models\Backend\Practice_questionModel;
use App\Models\Backend\Child_practice_answerModel;
use App\Models\Backend\Child_practice_question_answerModel;
use App\Models\Backend\Child_practice_question_listModel;
use App\Models\Backend\Child_practice_questionModel;
use App\Models\Backend\Child_pratice_mainModel;
use App\Models\Backend\Question_listModel;
use App\Models\Backend\Practice_questionlistModel;
use App\Models\Backend\Question_grouplistModel;

class SetworkController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    protected $prefix = 'front-end';
    protected $folder = 'dashboard-child';

    public function check_subject(Request $request){
        $html = "";
        $child = ChildModel::find(Auth::guard('child')->id());
        $subject_id = [];
        if($child){
            // Check ว่ามีตัวฟรีไหม
            $freePackage = User_packageModel::where(['child_id'=>$child->id])
                ->where('end_date',">=",date('Y-m-d'))->where('package_id',0)
            ->first();
            if($freePackage){
                $subjects = SubjectModel::where('isActive','Y')->get();
                    if($subjects){
                        foreach($subjects as $subject){
                            $subject_id[] = $subject->id;
                        }
                    } 
            }else{
                $child_package = User_packageModel::where(['child_id'=>$child->id])
                    ->where('end_date',">=",date('Y-m-d'))
                ->first();

                if($child_package){
                    $subjects = Package_subjectModel::where('package_id',$child_package->package_id)->get();
                    if($subjects){
                        foreach($subjects as $subject){
                            $subject_id[] = $subject->subject_id;
                        }
                    }
                }
            }
            // LOOP วิชาส่งค่ากลับไปที่สามารถเรียนได้
            if($subject_id != null){
                $subs = SubjectModel::whereIn("id",$subject_id)->where('isActive','Y')->get();
                if($subs){
                    $html .= "<div class='setwork-subject-list'>";
                    foreach($subs as $key=>$sub){
                        $checked = ($key == 0) ? "checked" : "";
                        $html .= '<input type="radio" class="btn-check subject-radio" name="subject_id" id="subject'.$sub->id.'" data-subject-id="'.$sub->id.'" value="'.$sub->id.'" '.$checked.' autocomplete="off">';
                        $html .= '<label class="subject-card" for="subject'.$sub->id.'">';
                        $html .=    '<img src="'.Helper::getImage($sub->image).'" alt="'.htmlspecialchars($sub->name, ENT_QUOTES).'" />';
                        $html .=    '<span>'.htmlspecialchars($sub->name, ENT_QUOTES).'</span>';
                        $html .= '</label>';
                    }
                    $html .= "</div>";
                }
            }
        }
        return response()->json(['success' => true, 'data' => $html]);
    }

    public function create_pratice(Request $request)
    {
        try { 
            DB::beginTransaction();
            // CHECK หา TOPIC ว่ามีโจทย์ตามที่กำหนดไหม
            $number_question = $request->number_question;
            $subject_topic = "";
            if($request->topic_id){
                $subject_topic = implode(",",$request->topic_id);
            }

            $level_id = "";
            if($request->level_id){
                $level_id = implode(",",$request->level_id);
            }
            // $check_question = QuestionModel::where(["subject_id"=>$request->subject_id, "grade_level_id"=>$request->grade_id, "isActive"=>'Y'])
            //     ->whereIn("topic_id",explode(',', $subject_topic))
            //     ->whereIn("hard_level",explode(',', $level_id))
            //     ->orderby("hard_level",'asc')
            //     ->inRandomOrder()
            // ->limit($number_question)->get();

            // $group_list = Question_grouplistModel::where(["question_id"=>$check_question->id])->get();

            // นำเอาข้อสอบทั้งหมดของ LEVEL นั้นมาก่อน
            $condition1 = QuestionModel::where(["subject_id"=>$request->subject_id, "grade_level_id"=>$request->grade_id, "isActive"=>'Y'])
                ->whereIn("hard_level",explode(',', $level_id))
                ->orderby("hard_level",'asc')
            ->get();
            if($condition1){
                $selected_groups = $request->input('group', []);
                $filtered_questions = $condition1->filter(function($question) use ($selected_groups) {
                    return Question_grouplistModel::where("question_id", $question->id)
                           ->where("type", "sub2")
                           ->whereIn("question_group_id", $selected_groups)
                           ->exists();
                });
                if ($filtered_questions->count() == 0) {
                    $arr = [
                        'status' => '500',
                        'result' => 'error',
                        'message' => 'ไม่สามารถทำรายการได้',
                        'desc' => 'ไม่พบข้อมูลเนื้อหาที่เลือก !'
                    ];
                }
                else{
                    $filtered_ids = $filtered_questions->pluck('id');
                    $check_question = QuestionModel::where(["subject_id"=>$request->subject_id, "grade_level_id"=>$request->grade_id, "isActive"=>'Y'])
                        ->whereIn("id", $filtered_ids)
                        ->whereIn("hard_level",explode(',', $level_id))
                        ->orderby("hard_level",'asc')
                        ->inRandomOrder()
                    ->limit($number_question)->get();
                    if($number_question > @$check_question->count()){
                        $arr = [
                            'status' => '500',
                            'result' => 'error',
                            'message' => 'ไม่สามารถทำรายการได้',
                            'desc' => "ข้อสอบไม่เพียงพอ มีทั้งหมด ".$check_question->count()." ข้อ ",
                        ];
                    }else{
                        $child = ChildModel::find(Auth::guard('child')->id());

                        $practice_name = trim($request->practice_name);
                        if (empty($practice_name)) {
                            $subject = SubjectModel::find($request->subject_id);
                            $subjectName = $subject ? $subject->name : 'Practice';
                            $date = date('j M');
                            $practice_name = $subjectName . ' Practice – ' . $date;
                        }

                        $data = new Child_pratice_mainModel();
                        $data->user_id = $child->user_id;
                        $data->child_id = Auth::guard('child')->id();
                        $data->title = $practice_name; // <--- (auto naming)!

                        $data->time_limit = $request->time_limit;
                        $data->number_question = $request->number_question;
                        $data->subject_id = $request->subject_id;
                        $data->grade_id = $request->grade_id;
                        $data->level_id = $level_id;
                        $data->subject_topic = $subject_topic;
                        $data->isType = "pratice";
                        if($data->save()){
                            if($check_question){
                                foreach($check_question as $key=>$question){
                                    $pratice_question = new Child_practice_questionModel();
                                    $pratice_question->child_practice_main_id = $data->id;
                                    $pratice_question->subject_id = $data->subject_id;
                                    $pratice_question->image = $question->image;
                                    $pratice_question->sku = $question->topic;
                                    $pratice_question->topic = $question->topic;
                                    $pratice_question->question = $question->question;
                                    $pratice_question->question_help = $question->question_help;
                                    $pratice_question->question_hint = $question->question_hint;
                                    $pratice_question->type_question = $question->type_question;
                                    $pratice_question->type_sort = $question->type_sort;
									$pratice_question->prefix = $question->prefix;
									$pratice_question->suffix = $question->suffix;
                                    $pratice_question->grade_level_id = $question->grade_level_id;
                                    $pratice_question->hard_level = $question->hard_level;
                                    $pratice_question->list_order = $key + 1;
                                    $pratice_question->isActive = $question->isActive;
                                    $pratice_question->type_math = $question->type_math;
                                    if($pratice_question->save()){
                                        $child_answer = new Child_practice_question_answerModel();
                                        $child_answer->child_id = $data->child_id;
                                        $child_answer->child_practice_main_id = $data->id;
                                        $child_answer->child_practice_question_id = $pratice_question->id;
                                        $child_answer->list_order = $pratice_question->list_order;
                                        $child_answer->correct = "W";
                                        $child_answer->save();
    
                                        $question_lists = Question_listModel::where(["question_id"=>$question->id])->get();
                                        if($question_lists){
                                            foreach($question_lists as $lists){
                                                $practice_lists = new Child_practice_question_listModel();
                                                $practice_lists->child_practice_main_id = $data->id;
                                                $practice_lists->child_practice_question_id = $pratice_question->id;
                                                $practice_lists->answer_image = $lists->answer_image;
                                                $practice_lists->answer_text = $lists->answer_text;
                                                $practice_lists->correct_status = $lists->correct_status;
                                                $practice_lists->list_order = $lists->list_order;
                                                $practice_lists->list_answer = $lists->list_answer;
                                                $practice_lists->isActive = $lists->isActive;
                                                $practice_lists->save();
                                            }
                                        }
                                    }
                                }
                            }
    
                             $arr = [
                                 'status' => '200',
                                 'result' => 'success',
                                 'message' => 'ดำเนินการสำเร็จ'
                             ];
                        }else{
                             $arr = [
                                 'status' => '500',
                                 'result' => 'error',
                                 'message' => 'ไม่สามารถทำรายการได้',
                                 'desc' => 'ชื่อผู้ใช้งาน หรือรหัสผ่านไม่ถูกต้อง !'
                             ];
                        }
                    }
                }

            }
            DB::commit();
           echo json_encode($arr);
        } catch (\Exception $e) {
            DB::rollBack();
            $error_log = $e->getMessage();
            $error_line = $e->getLine();
            $type_log = 'backend';
            $error_url = url()->current();
            $log = LogsController::logInsert($error_line, $error_url, $error_log, $type_log);
            $arr = [
                'status' => '500',
                'message' => 'Something broke.',
                'desc' => "Error Code($log) !"
            ];
            echo json_encode($arr);
        }
    }
}
