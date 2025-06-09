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
use App\Models\Backend\Child_practice_answerModel;
use App\Models\Backend\Child_practice_question_answerModel;
use App\Models\Backend\Child_practice_question_listModel;
use App\Models\Backend\Child_practice_questionModel;
use App\Models\Backend\Child_pratice_mainModel;
use Intervention\Image\ImageManagerStatic as Image;

use App\Models\Backend\UserModel;
use App\Models\Backend\ChildModel;
use App\Models\Backend\PracticeModel;
use App\Models\Backend\Order_headModel;
use App\Models\Backend\PackageModel;
use App\Models\Backend\Practice_questionlistModel;
use App\Models\Backend\Practice_questionModel;
use App\Models\Backend\User_packageModel;
use App\Models\Backend\Setting_packageModel;
use App\Models\Backend\SubjectModel;

class PracticeController extends Controller
{
    protected $prefix = 'front-end';
    protected $folder = 'dashboard-child';

    public function index(Request $request,$practice_id)
    {
        $child_id = Auth::guard('child')->id();
        $child = ChildModel::findOrFail($child_id);
        $main = Child_pratice_mainModel::where(['uuid'=>$practice_id, "child_id"=>$child_id])->first();
        if($main){
            $answer_practices = Child_practice_question_answerModel::where(["child_id"=>$child_id, "child_practice_main_id"=>$main->id])->where('correct','W')->first();
            if($main->start_date == NULL){
                $main->start_date = date('Y-m-d H:i:s');
                if($main->time_limit != 0){
                    $main->end_date = date('Y-m-d H:i:s',strtotime("+ $main->time_limit min"));
                }
                $main->save();
            }
            if($main->isActive != "S"){
                if($main->end_date > date('Y-m-d H:i:s') || $main->time_limit == 0)
                {
                    if($answer_practices){ // กรณีมีข้อสอบ
                        if(@$answer_practices->start_date == NULL){
                            $answer_practices->start_date = date('Y-m-d H:i:s');
                            $answer_practices->save();
                        }
                        if($main->isActive != "I")
                        {
                            $main->isActive = "I";
                            $main->save();
                        }
                        return view("$this->prefix.$this->folder.practicev2", [
                            'prefix' => $this->prefix,
                            'folder' => $this->folder,
                            'child' => $child,
                            'pratice' => $main,
                            'question_pratice' => $answer_practices,
                            'practice_id' => $practice_id,
                            'question' =>  Child_practice_questionModel::where('id',$answer_practices->child_practice_question_id)->first(),
                            'answers' => Child_practice_question_listModel::where(["child_practice_question_id"=>$answer_practices->child_practice_question_id])->get(),
                        ]);
                    }
                    else{ // กรณีไม่มีข้อสอบ
                        return view("$this->prefix.$this->folder.practice-thankyou", [
                            'prefix' => $this->prefix,
                            'folder' => $this->folder,
                            'child' => $child,
                            'main' => $main,
                        ]);
                    }
                }else{
                    $main->isActive = "S";
                    $main->complete_date = date('Y-m-d H:i:s');
                    if($main->save()){
                        $streak_point = $child->streak_point;
                        $child->streak_point = $streak_point + 1;

                        $wallet_point = $child->wallet_point;
                        $child->wallet_point = $wallet_point + 1;
                        $child->save();
                    }
                    return view("$this->prefix.$this->folder.practice-thankyou", [
                        'prefix' => $this->prefix,
                        'folder' => $this->folder,
                        'child' => $child,
                        'main' => $main,
                    ]);
                }
            }else{
                return view("$this->prefix.$this->folder.practice-thankyou", [
                    'prefix' => $this->prefix,
                    'folder' => $this->folder,
                    'child' => $child,
                    'main' => $main,
                ]);
            }
        }else{
            return redirect("dashboard-child");
        }
    }

 
    public function review(Request $request, $practice_id){
        $child_id = Auth::guard('child')->id();
        $pratice = PracticeModel::where(['id'=>$practice_id, "child_id"=>$child_id])->first();
        if(@$pratice){

            $practice_question = Child_practice_answerModel::where(["id"=>$request->question,"child_id"=>$child_id])->first();
            if($practice_question){
                $item = Child_practice_answerModel::where("child_id",$child_id)->where('id','>',$request->question)->where('practice_id',$practice_id)->first();
                $back_item = Child_practice_answerModel::where("child_id",$child_id)->where('id','<',$request->question)->where('practice_id',$practice_id)->orderby('id',"desc")->first();

                if($item){ // กรณีมีต่อ
                    $next = $item->id;
                    
                }else{
                    $next = "";
                }

                if($back_item){
                    $back = $back_item->id;
                }else{
                    $back = "";
                }
            }else{
                return redirect("dashboard-child");
            }
            $question = Practice_questionModel::where('id',$practice_question->question_id)->first();

            $child = ChildModel::findOrFail($child_id);
            return view("$this->prefix.$this->folder.practice-review", [
                'prefix' => $this->prefix,
                'folder' => $this->folder,
                'child' => $child,
                'pratice' => $pratice,
                'question_pratice' => $practice_question,
                'question' => $question,
                'answers' => Practice_questionlistModel::where(["question_id"=>$practice_question->question_id])->get(),
                'next' => $next,
                'back' => $back,
            ]);
        }else{
            return redirect("dashboard-child");
        }
    }

    // NEW VERSION
    public function check_answer(Request $request){
        try {
            $arr = [];
            $correct_choice = []; // ข้อที่ถูกต้อง
            $false_choice = []; // ข้อที่ผิด
            $selected_answers = $request->selected_answers;
            $answer_sort = explode(",",$request->selected_answers);
            $correct = "Y";
            $type_sort = "N";
            $answer_input = $request->answer_input;
            $type_sort = $request->type_sort;

            // == NEW UPDATE 01/05/2025
            $correct_selected = []; // ตอบถูก
            $wrong_selected = []; // ตอบผิด
            $unselected_but_correct = []; // คำตอบที่ถูก แต่ไม่ได้เลือก
            //==== END

            $answers = explode(",",$request->answers);
            $correctAnswers = [];

            DB::beginTransaction();
            $child_id = Auth::guard('child')->id();
            $data = Child_practice_question_answerModel::where(["child_id"=>$child_id, "child_practice_main_id"=>$request->practice_id, "child_practice_question_id"=>$request->question_id])
                ->where('correct','W')
            ->first();
            $question = Child_practice_questionModel::find($request->question_id);
            if($question){ $type_sort = $question->type_sort; }
            $question_data_type = "text";
            if($data){
                if($question->type_sort != "Y"){ // แบบไม่เรียงลำดับ
                    if($question->type_question == "text" || $question->type_question == "image"){
                        $correct_question = Child_practice_question_listModel::where(["child_practice_main_id"=>$request->practice_id, "child_practice_question_id"=>$request->question_id, "correct_status"=>"1"])->get();

                        if($correct_question){
                            foreach($correct_question as $cr_question){
                                array_push($correct_choice,$cr_question->id);
                            }
                        }
                        $correct_question_count = $correct_question->count();
                        $correct_count = 0;
                        $correct = $data->correct;
                        
                        if($answer_sort){
                            foreach($answer_sort as $answer){
                                $question_list = Child_practice_question_listModel::find($answer);
                                if($question_list->correct_status == "1"){ $correct_count++; }
                                else if($question_list->correct_status != "1")
                                { 
                                    array_push($false_choice,$question_list->id);
                                    $correct_count--; 
                                }
                            }
                        }
                        if($correct_count == $correct_question_count){ // เช็คว่าจำนวน Count ข้อสอบที่เราตอบถูกครบทุกข้อไหม
                            $correct = "Y";
                        }else{
                            $correct = "N";
                        }
        
                        $data->practice_question_list_id = $selected_answers;
                        if($data->correct == "W"){
                            $data->correct_answer = implode(",",$correct_choice);
                            $data->correct = $correct;
                        }
                    }
                    else if($question->type_question == "input"){
                        $correct_question = Child_practice_question_listModel::where(["child_practice_main_id"=>$request->practice_id
                        , "child_practice_question_id"=>$request->question_id
                        , "answer_text"=>"$answer_input"])->first();
                        if($correct_question){
                            $correct = "Y";
                        }else{
                            $correct = "N";
                        }

                        if($correct_question){
                            if($data->correct == "W"){
                                $data->practice_question_list_id = $answer_input;
                                $data->correct_answer = @$correct_question->answer_text;
                            }
                        }else{
                            if($data->correct == "W")
                            {
                                $correct_question = Child_practice_question_listModel::where(["child_practice_main_id"=>$request->practice_id
                                , "child_practice_question_id"=>$request->question_id])->first();
                                $data->practice_question_list_id = $answer_input;
                                $data->correct_answer = @$correct_question->answer_text;

                                
                            }
                        }
                        $correct_choice = @$correct_question->answer_text;
                        $false_choice = $answer_input;
                        $data->correct = $correct;
                        $question_data_type = "input";
                    }
                    
                }else{
                    $correct_question = Child_practice_question_listModel::where(["child_practice_question_id"=>$request->question_id])->orderby('list_answer','asc')->get();
                    if($correct_question){
                        $false_choice = explode(",",$request->selected_answers);
                        foreach($correct_question as $key=>$question){
                            array_push($correct_choice, $question->id);
                            if(@$question->id != @$answers[$key]){
                                $correct = "N";
                            }
                        }

                        foreach ($answers as $index => $answer) {
                            $correct_answer = 0;
                            $check_correct = Child_practice_question_listModel::where(["child_practice_question_id"=>$request->question_id])->where("id",$answer)->where("list_answer","!=",null)->first();
                            $index_sort = $index+1;
                            if($check_correct){
                                if($check_correct->list_answer == $index_sort){
                                    $correct_answer = 1;
                                }
                                else{
                                    $correctAnswers[] = [
                                        'index' => $index_sort,
                                        'is_correct' => 0
                                    ];
                                }
                                if($correct_answer == 1){
                                    $correctAnswers[] = [
                                        'index' => $index_sort,
                                        'is_correct' => 1,
                                    ];
                                }
                            }else{
                                $correctAnswers[] = [
                                    'index' => $index_sort,
                                    'is_correct' => 0
                                ];
                            }
                        }
                        $data->practice_question_list_id = $request->answers;
                        $data->correct_answer = implode(",",$correct_choice);
                        $data->correct = $correct;
                        $arr['correctAnswers'] = $correctAnswers;
                        $question_data_type = "sort";
                    }
                }
                
                $data->end_date = date('Y-m-d H:i:s');
                if($data->save()){
                    $count_total_question = Child_practice_question_answerModel::where(["child_id"=>$child_id,"child_practice_main_id"=>$request->practice_id])->where('correct','!=','W')->count();
                    $correct_count = Child_practice_question_answerModel::where(["child_id"=>$child_id,"child_practice_main_id"=>$request->practice_id])->where('correct','Y')->count();
                    $pratice = Child_pratice_mainModel::findOrFail($request->practice_id);

                    $pratice->total_question = $count_total_question;
                    $pratice->correct_answer = $correct_count;
                    if($count_total_question == $pratice->number_question){ 
                        $pratice->isActive = "S"; 
                        $pratice->complete_date = date('Y-m-d H:i:s');

                        $child_id = Auth::guard('child')->id();
                        $child = ChildModel::findOrFail($child_id);

                        $streak_point = $child->streak_point;
                        $child->streak_point = $streak_point + 1;

                        $wallet_point = $child->wallet_point;
                        $child->wallet_point = $wallet_point + 1;
                        $child->save();
                    }
                    $pratice->save();
               

                    $type_question = "pratice";
                    $main = Child_pratice_mainModel::find($question->child_practice_main_id);
                    if($main->isType == "mocktest"){
                        $type_question = "mocktest";
                    }else{
                        if($correct == "Y"){
                            $arr['status'] = 200;
                            $arr['message'] = "That’s right - You aced it! +1 coin";
                        }else{ 
                            $arr['status'] = 500;
                            $arr['message'] = "Oh no! Better luck next time.";
                        }

                        if($question->type_question != "input")
                        {
                            $arr['type_question'] = "$type_question";
                            $arr['type'] = $type_sort; // ข้อที่ตอบ
                            $arr['answer_question'] = $request->selected_answers; // ข้อที่ตอบ
                            $arr['true_answer'] = implode(",",@$correct_choice); // ข้อที่ถูกทั้งหมด
                            $arr['false_answer'] = implode(",",@$false_choice); // ข้อที่ตอบผิด

                        }else{
                            $arr['type_question'] = "$type_question";
                            $arr['type'] = @$type_sort;
                            $arr['answer_question'] = $request->answer_input; // ข้อที่ตอบ
                            $arr['true_answer'] = $correct_choice; // ข้อที่ถูกทั้งหมด
                            $arr['false_answer'] = $false_choice; // ข้อที่ตอบผิด
                        }
                        $arr['question_data_type'] = $question_data_type;
                    }
                }
            }
            
            else{
                $arr['status'] = 500;
                $arr['message'] = "Oh no!";
            }
            DB::commit();
            return json_encode($arr);
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
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


    
    public function transcript(Request $request,$practice_id)
    {
        $child_id = Auth::guard('child')->id();
        $child = ChildModel::findOrFail($child_id);

        $practice = Child_pratice_mainModel::where(["uuid"=>$practice_id,"child_id"=> $child_id])->first();
        return view("$this->prefix.$this->folder.result-summary", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'child' => $child,
            'practice' => $practice,
            'questions' => Child_practice_questionModel::where(["child_practice_main_id"=>$practice->id])->get(),
        ]);
       
     
    }
	
	// New Scorecard
    public function scorecard($uuid)
    {
        $child_id = Auth::guard('child')->id();
        $practice = \App\Models\Backend\Child_pratice_mainModel::where([
            'uuid' => $uuid,
            'child_id' => $child_id,
        ])->first();

        if (!$practice) {
            return response()->json(['error' => 'Not found'], 404);
        }

        // Eager load related question (1 query for answers, 1 for questions)
        $answers = \App\Models\Backend\Child_practice_question_answerModel::with('question')
            ->where([
                'child_practice_main_id' => $practice->id,
                'child_id' => $child_id,
            ])
            ->get();

        $questions = [];

        foreach ($answers as $ans) {
            $q = $ans->question; // Already loaded

            if (!$q) continue; // Skip if question not found

            // Time spent formatting
            $time_spent = null;
            if ($ans->start_date && $ans->end_date) {
                $seconds = strtotime($ans->end_date) - strtotime($ans->start_date);
                $time_spent = gmdate($seconds >= 3600 ? 'H:i:s' : 'i:s', $seconds);
            }

            // === Get user's answer in readable format ===
            $user_answer_text = '';
            $correct_answer_text = '';

            // For MCQ/DND: fetch from Child_practice_question_listModel
            if ($q->type_question == 'text' || $q->type_question == 'image') {
                // Get selected options (may be comma-separated)
                $selected_ids = array_filter(explode(',', (string) $ans->practice_question_list_id));
                $correct_ids = array_filter(explode(',', (string) $ans->correct_answer));

                $user_answer_text = implode(', ',
                    \App\Models\Backend\Child_practice_question_listModel::whereIn('id', $selected_ids)->pluck('answer_text')->toArray()
                );

                $correct_answer_text = implode(', ',
                    \App\Models\Backend\Child_practice_question_listModel::whereIn('id', $correct_ids)->pluck('answer_text')->toArray()
                );
            }
            // For input: just show the answer as is
            else if ($q->type_question == 'input') {
                $user_answer_text = $ans->practice_question_list_id;
                $correct_answer_text = $ans->correct_answer;
            }

            // Use only Explanation field, not hint
            $explanation = $q->question_help ?? '';

            $questions[] = [
                'id' => $q->id,
                'text' => $q->question ?? '',
                'is_correct' => $ans->correct === 'Y',
                'user_answer' => $user_answer_text,
                'correct_answer' => $correct_answer_text,
                'explanation' => $explanation,
                'time_spent' => $time_spent,
            ];
        }

        return response()->json([
            'practice' => [
                'title' => $practice->title ?? '',
                'correct_answer' => $practice->correct_answer ?? 0,
                'total_question' => $practice->total_question ?? 0,
                // Add more if you wish
            ],
            'questions' => $questions,
        ]);
    }

}

