<?php

namespace App\Http\Controllers\Frontend\Child;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Backend\LogsController;
use App\Helpers\Helper;
use App\Http\Controllers\Functions\FunctionControl;
use App\Models\Backend\Child_mocktest_question_topicModel;
use App\Models\Backend\Child_pratice_mainModel;
use Intervention\Image\ImageManagerStatic as Image;

use App\Models\Backend\UserModel;
use App\Models\Backend\ChildModel;
use App\Models\Backend\Content_worksheet_subModel;
use App\Models\Backend\Content_WorksheetModel;
use App\Models\Backend\PracticeModel;
use App\Models\Backend\Order_headModel;
use App\Models\Backend\PackageModel;
use App\Models\Backend\User_packageModel;
use App\Models\Backend\Setting_packageModel;
use App\Models\Backend\SubjectModel;
use App\Models\Backend\Grade_levelModel;
use App\Models\Backend\Mocktest_topicModel;
use App\Models\Backend\JourneyMd;
use App\Models\Backend\Question_groupModel;

class DashboardController extends Controller
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

    public function index(Request $request)
    {
        
        $child = ChildModel::findOrFail(Auth::guard('child')->id());
        $count_point = 0;
        $count_point = Child_pratice_mainModel::where("child_id",Auth::guard('child')->id())->where("isActive","S")->sum("correct_answer");
        return view("$this->prefix.$this->folder.index", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'title_page' => "Hi, ".@$child->fullname(),
            'sub_title_page' => "Complete <a href='javascript:void(0)'>a task today</a> to earn ten coins.",
            'child' => $child,
            'count_point' => $count_point,
            'pratices' => Child_pratice_mainModel::where(['user_id'=>$child->user_id])->where('child_id',$child->id)
                ->whereIn('isActive',["W","I"])
                ->orderby('id','desc')
            ->get(),

            'navbar_name' => "Hi, ".$child->fullname(),
            'navbar_detail' => 'Complete <a href="javascript:void(0)">a task today</a> to earn ten coins.',
        ]);
    }

    public function journey(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());

        $keyword = Arr::get($request, 'keyword');
        $status = Arr::get($request, 'status');
        $paginate = Arr::get($request, 'total', 15);
        $query = new JourneyMd;
        if ($keyword) {
            $query = $query->where('name', 'LIKE', '%' . trim($keyword) . '%');
        }
        if ($status) {
            $query = $query->where('isActive', $status);
        }

        $data = $query->orderBy('list_order')->paginate($paginate);


        return view("$this->prefix.$this->folder.journey", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'title_page' => "Learning Journey",
            'sub_title_page' => "Step-by-step journeys to mastery.",
            'child' => $child,
            'rows' => $data,
            'navbar_name' => "Learning Journey",
            'navbar_detail' => 'Step-by-step journeys to mastery.',
        ]);
    }
    
    public function review(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());

        $isType = $request->type;
        $data = Child_pratice_mainModel::where(['user_id'=>$child->user_id, 'child_id'=>$child->id]);
        if($isType){
            $data = $data->where("isType",$isType);
        }
        $data = $data->orderby('id','desc')->get();
        // Child_pratice_mainModel::where(['user_id'=>$child->user_id])->where('child_id',$child->id)->orderby('id','desc')->get()

        return view("$this->prefix.$this->folder.review", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'title_page' => "Review",
            'sub_title_page' => "Let's look at your on-progress, and completed tasks",
            'child' => $child,
            'pratices' => $data,
            'navbar_name' => "Review",
            'navbar_detail' => "Let's look at your on-progress, and completed tasks",
        ]);
    }

    public function hangouts(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());
        return view("$this->prefix.$this->folder.hangouts", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'title_page' => "hangouts",
            'sub_title_page' => "Let's look at your on-progress, and completed tasks",
            'child' => $child,
            'navbar_name' => "Hangouts",
            'navbar_detail' => "Recruit or unlock new companions to take on your journey.",
        ]);
    }

    
    public function quest(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());
        return view("$this->prefix.$this->folder.quest", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'child' => $child,
        ]);
    }

    public function worksheet(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());
        return view("$this->prefix.$this->folder.worksheet", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'title_page' => "Worksheets",
            'sub_title_page' => "Check your practice and mock tests results",
            'child' => $child,
            'worksheets' => Content_WorksheetModel::where("isActive","Y")->get(),
            'navbar_name' => "Worksheets",
            'navbar_detail' => "Check your practice and mock tests results",
        ]);
    }
   
    public function badge_detail(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());
        return view("$this->prefix.$this->folder.badge-detail", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'child' => $child,
        ]);
    }

    
    public function setwork(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());
        return view("$this->prefix.$this->folder.set-work", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'title_page' => "Hi, ".@$child->fullname(),
            'sub_title_page' => "Complete <a href='javascript:void(0)'>a task today</a> to earn ten coins.",
            'child' => $child,
            'subjects' => SubjectModel::where('isActive','Y')->orderby('list_order','asc')->get(),
            'grades' => Grade_levelModel::where('isActive','Y')->get(),
            'childs' => ChildModel::where(["user_id"=>Auth::guard('user')->id(), "isActive"=>'Y'])->get(),
            'mocktests' => Mocktest_topicModel::where("isActive",'Y')->orderby('list_order','asc')->get(),
            'navbar_name' => "Hi, ".$child->fullname(),
            'navbar_detail' => 'Complete <a href="javascript:void(0)">a task today</a> to earn ten coins.',
        ]);
    }
    
    public function profile(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());

        $count_practice = Child_pratice_mainModel::where(["user_id"=>$child->user_id, "child_id"=>$child->id, "isActive"=>"S", "isType"=>"pratice"])->count();
        $count_mocktest = Child_pratice_mainModel::where(["user_id"=>$child->user_id, "child_id"=>$child->id, "isActive"=>"S", "isType"=>"mocktest"])->count();

        return view("$this->prefix.$this->folder.profile", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'child' => $child,
            'count_practice' => $count_practice,
            'count_mocktest' => $count_mocktest,
            'navbar_name' => "Hi, ".$child->fullname(),
            'navbar_detail' => '',
        ]);
    }

    public function setting_profile(Request $request)
    {
        $child = ChildModel::findOrFail(Auth::guard('child')->id());

        $count_practice = Child_pratice_mainModel::where(["user_id"=>$child->user_id, "child_id"=>$child->id, "isActive"=>"S", "isType"=>"pratice"])->count();
        $count_mocktest = Child_pratice_mainModel::where(["user_id"=>$child->user_id, "child_id"=>$child->id, "isActive"=>"S", "isType"=>"mocktest"])->count();

        return view("$this->prefix.$this->folder.setting-profile", [
            'prefix' => $this->prefix,
            'folder' => $this->folder,
            'child' => $child,
            'count_practice' => $count_practice,
            'count_mocktest' => $count_mocktest,
        ]);
    }
    

    public function check_child($id = null, $username)
    {
        if ($id == null) {
            $check = ChildModel::where('username', $username)->first();
            if ($check) 
            {
                $check_true = "no";
            } else {
                $check_true = "yes";
            }
        } else {
            $check = ChildModel::where('username', $username)->where('id', '!=', $id)->first();
            if ($check) {
                $check_true = "no";
            } else {
                $check_true = "yes";
            }
        }
        return $check_true;
    }

    public function profileUpdate(Request $request)
    {
        try {
            DB::beginTransaction();
            $birthdate = date('Y-m-d', strtotime($request->birthdate));
            $child_id = Auth::guard('child')->id();
            $check = $this->check_child($child_id,$request->username);
            if($check != "yes")
            {
                $arr['status'] = 501;
                $arr['message'] = "Something went wrongs.";
                $arr['desc'] = "Username is already exists.";
                echo json_encode($arr);
            }else{
                $data = ChildModel::findOrFail($child_id);
                $data->username = $request->username;
                if($request->password != null){ $data->password = bcrypt($request->password); }
                $data->firstname = $request->firstname;
                $data->lastname = $request->lastname;
                $data->birthdate = $birthdate;
                $data->year_group = $request->year_group;
                $file = $request->image;
                if($file){
                    Storage::disk(env('uploadwith'))->delete($data->image);
                    $image = FunctionControl::crop_image($file, 'child',150,150);
                    $data->image = $image;
                }
                if ($data->save()) {
                    DB::commit();
                    $arr['status'] = 200;
                    $arr['message'] = "Successfully.";
                    $arr['desc'] = "";
                } else {
                    $arr['status'] = 500;
                    $arr['message'] = "Something went wrongs.";
                    $arr['desc'] = "Please try again";
                }
                echo json_encode($arr); 
            }
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

    public function logOut()
    {
        if (!Auth::guard('child')->logout()) {
            return redirect("");
        }
    }

    public function parentLogout(Request $request)
    {
        $parent_id = $request->parent_id;
        if($parent_id != null){
            $check = ChildModel::where(["id"=>Auth::guard('child')->id(), "user_id"=>$parent_id])->first();
            if($check){
                $child = Auth::guard('child')->logout();
                $data = Auth::guard('user')->loginUsingId($check->user_id);
                $arr = [
                    'status' => '200',
                    'result' => 'success',
                    'message' => 'ดำเนินการสำเร็จ',
                ];
            }
        }
        echo json_encode($arr);
        // if (!Auth::guard('child')->logout()) {
        //     return redirect("");
        // }
    }
    
    public function parentCheck(Request $request)
    {
        $parent_id = $request->parent_id;
        if($parent_id != null){
            $check = ChildModel::where(["id"=>Auth::guard('child')->id(), "user_id"=>$parent_id])->first();
            if($check){
                $arr = [
                    'status' => '200',
                    'result' => 'Success',
                ];
            }
        }
        echo json_encode($arr);
    }
    
    
}
