<?php

namespace App\helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Backend\AdminModel;
use App\Models\Backend\Content_settingModel;
use App\Models\Backend\Setting_cdnimageModel;
use App\Models\Backend\Seo_friendlyModel;
use App\Models\Backend\User_packageModel;
use App\Models\Backend\Order_headModel;
use App\Models\Backend\PackageModel;
use App\Models\Backend\Setting_scriptModel;
use DateTime;
use DB;

class Helper 
{
    protected $prefix = 'back-end';
    //==== Menu Active ====
    public static function auth_menu()
    {
        return view("back-end.alert.alert",[
            'url'=> "webpanel",
            'title' => "เกิดข้อผิดพลาด",
            'text' => "คุณไม่ได้รับสิทธิ์ในการใช้เมนูนี้ ! ",
            'icon' => 'error'
        ]); 
    }


    public static function getRandomID($size, $table, $column_name)
    {
        $check_status = 0;
        while ($check_status == 0) 
        {
            $random_id = Helper::randomCode($size);

            $data = DB::table($table)->where("$column_name","$random_id")->get();
            if($data->count() == 0)
            {
                $check_status = 1;
            }
        }
        return $random_id;
    }

    public static function getRandomIDNumber($size, $table, $column_name)
    {
        $check_status = 0;
        while ($check_status == 0) 
        {
            $random_id = Helper::randomCodeNumber($size);

            $data = DB::table($table)->where("$column_name","$random_id")->get();
            if($data->count() == 0)
            {
                $check_status = 1;
            }
        }
        return $random_id;
    }

    public static function randomCode($length)
    {
        $possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghigklmnopqrstuvwxyz"; //ตัวอักษรที่ต้องการสุ่ม
        $str = "";
        while (strlen($str) < $length) {
            $str .= substr($possible, (rand() % strlen($possible)), 1);
        }
        return $str;
    }

    public static function randomCodeNumber($length)
    {
        $possible = "1234567890"; //ตัวอักษรที่ต้องการสุ่ม
        $str = "";
        while (strlen($str) < $length) {
            $str .= substr($possible, (rand() % strlen($possible)), 1);
        }
        return $str;
    }

    public static function convertThaiDate($date, $type = 'date')
    {
        $thai_months = [
            1 => 'ม.ค.',
            2 => 'ก.พ.',
            3 => 'มี.ค.',
            4 => 'เม.ย.',
            5 => 'พ.ค.',
            6 => 'มิ.ย.',
            7 => 'ก.ค.',
            8 => 'ส.ค.',
            9 => 'ก.ย.',
            10 => 'ต.ค.',
            11 => 'พ.ย.',
            12 => 'ธ.ค.',
        ];
        $date = Carbon::parse($date);
        $month = $thai_months[$date->month];
        $year = $date->year + 543;

        if ($type == 'datetime') {
            return $date->format("j $month $year H:i:s");
        }

        return $date->format("j $month $year");
    }


    public static function typeLogs($type){
        $html = "";
        if($type == "Error"){
            $html.='
            <div class="symbol symbol-35px me-3">
                <i class="fa-sharp fa-solid fa-circle-exclamation text-danger"></i>
            </div>
            <span class="fw-semibold text-danger"><b>Error</b></span>';
        }
        return $html;
    }

    public static function typeLogsSystem($type){
        $html = "";
        if($type == "backend"){
            $html.='
            <span class="fw-semibold text-info"><b>Backend</b></span>';
        }else if($type == "frontend"){
            $html.='
            <span class="fw-semibold text-primary"><b>Frontend</b></span>';
        }
        return $html;
    }

    public static function isActive($status)
    {
        $data = "";
        if ($status == 'Y') {
            $data = '<i style="font-size:20px;" class="fa fa-check-circle text-success"></i> Active';
        } elseif ($status == 'N') {
            $data = '<i style="font-size:20px;" class="fa fa-times-circle text-danger"></i> Inactive';
        }
        return $data;
    }
    
    public static function cuby($id){
        $data = "";
        $item = AdminModel::find($id);
        if($item){
            $data = $item->username;
        }
        return $data;
    }

    public static function Status($status)
    {
        $data = "";
        if ($status == 'Y') {
            $data = 'bg-success';
        } elseif ($status == 'N') {
            $data = 'bg-danger';
        }
        return $data;
    }

    public static function decryptDB($payload)
    {
        $secretKey = env('secretKey');
        $payload = base64_decode($payload);
        $iv = substr($payload, 0, 16);
        $payload = substr($payload, 16);
        return openssl_decrypt($payload, 'AES-256-CBC', $secretKey, 0, $iv);
    }

    public static function contentUpdate($id){
        $data = Content_settingModel::find($id);
        if($data->setting_type == 1 || $data->setting_type == 2 || $data->setting_type == 3){
            $html = '<a href="javascript:void(0);" onclick="updateModal('.$id.');" class="btn btn-sm btn-warning" style="margin-right:6px;"><i class="fa fa-edit"></i> Edit</a>';
        }
        elseif($data->setting_type == 4){
            $html = '<a href="javascript:void(0);" onclick="updateModal('.$id.');" class="btn btn-sm btn-warning" style="margin-right:6px;"><i class="fa fa-image"></i> Edit Image</a>';
        }
        return $html;
    }

    public static function contentUpdateLink($id,$link_id){
        $data = Content_settingModel::find($id);
        if($data->setting_type == 1 || $data->setting_type == 2 || $data->setting_type == 3){
            $html = '<a href="javascript:void(0);" onclick="updateModalLink('.$id.','.$link_id.');" class="btn btn-sm btn-warning" style="margin-right:6px;"><i class="fa fa-edit"></i> Edit</a>';
        }
        elseif($data->setting_type == 4){
            $html = '<a href="javascript:void(0);" onclick="updateModalLink('.$id.','.$link_id.');" class="btn btn-sm btn-warning" style="margin-right:6px;"><i class="fa fa-image"></i> Edit Image</a>';
        }
        return $html;
    }

    public static function seoUpdate($id){
        $data = Content_settingModel::find($id);
        $html = '<a href="javascript:void(0);" onclick="seoModal('.$id.');" class="button button-mini button-red"><i class="fa fa-cog"></i> Seo Setting</a>';
        return $html;
    }

    public static function contentGet($id){
        $data = Content_settingModel::find($id);
        if($data->setting_type == 1 || $data->setting_type == 4){
            $html = "$data->setting_varchar";
            if($data->setting_type == 4){
                $cdn = Setting_cdnimageModel::find(1);
                $cdn_link = "";
                if($cdn->isActive == 'Y'){ $cdn_link = $cdn->path_link; }
                    $html = $cdn_link.$data->setting_varchar;
                if($data->setting_varchar == null){
                    $html = $cdn_link."noimage.jpg";
                }
            }
            
        }
        elseif($data->setting_type == 2){
            $html = "$data->setting_text";
        }
        elseif($data->setting_type == 4){
            $html = '<a href="javascript:void(0);" onclick="updateModal('.$id.');" class="btn btn-sm btn-warning" style="margin-right:6px;"><i class="fa fa-image"></i> Edit Image</a>';
        }
        else{
            $html = "";
        }
        return $html;
    }


    public static function showImage($image = null){
        $html = "";
        if($image == null){
            $html = "<style>.image-input-placeholder { background-image: url('backend/assets/media/svg/files/blank-image.svg'); } [data-theme='dark'] .image-input-placeholder { background-image: url('assets/media/svg/files/blank-image-dark.svg'); }</style>";
        }else{
            $html = "<style>.image-input-placeholder { background-image: url('$image'); } [data-theme='dark'] .image-input-placeholder { background-image: url('$image'); }</style>";

        }
        return $html;
    }

    public static function getImage($image){
        $cdn = Setting_cdnimageModel::find(1);
        $imageShow = "";
        $cdn_link = "";
        if($cdn->isActive == 'Y'){ $cdn_link = $cdn->path_link; }
        $imageShow = $cdn_link.$image;
        return $imageShow;
    }

    public static function getFile($file){
        $cdn = Setting_cdnimageModel::find(1);
        $imageShow = "";
        $cdn_link = "";
        if($cdn->isActive == 'Y'){ $cdn_link = $cdn->path_link; }
        $imageShow = $cdn_link.$file;
        return $imageShow;
    }

    public static function seoGet($id){
        $data = Seo_friendlyModel::find($id);
        return $data;
    }

    public static function expire_package($end_date){
        if($end_date != null){
            $date_now = strtotime(date('Y-m-d'));
            $end_package = strtotime($end_date);
            $days_remaining = ($end_package - $date_now) / (60 * 60 * 24) + 1;
        }else{
            $days_remaining = 0;
        }
        

        return number_format($days_remaining,0);
    }


    public static function cal_upgrade_package(Request $request){
        $arr = [];
        $arr['price'] = 0;

        $date = date('Y-m-d');
        
        $old_price_package = 0;
        $package = PackageModel::find($request->package_id);
        $user_package = User_packageModel::where(["child_id"=> $request->child_id])->where('isActive','Y')->where('package_id','!=',0)->first();
        if($user_package){
            $order = Order_headModel::find($user_package->order_id);
            $type_order_date = 30;

            $package_price = $package->price;
            if($user_package->isType == "year"){ 
                $type_order_date = 365;
                $package_price = $package->price_year;
            }
            $end_days = Helper::expire_package($user_package->end_date);
            $end_days_cal = $end_days;
            if(@$order->free_date > 0){
                $end_days_cal = $end_days - $order->free_date;
            }

            $remaining_price = floor($order->price / $type_order_date * $end_days_cal); // ยอดเงินของ Package เดิม
            $price = ceil(($package_price / $type_order_date * $end_days_cal) - $remaining_price); // ยอดเงินต้องจ่ายเพื่ออัพแพ็คเกจ
            $arr['status'] = 200;
            $arr['price'] = $price;
            $arr['priceshow'] = number_format($price,0);
            $arr['end_days'] = $end_days;
        }
        else{
            $arr['status'] = 500;
            $arr['message'] = "Not found";
        }
        return $arr;
    }

    public static function cal_sell_upgrade_package(Request $request){
        $arr = [];
        $arr['price'] = 0;

        $date = date('Y-m-d');
        $old_price_package = 0;

        $package = PackageModel::find($request->package_id);
        $package_price = $package->price;
        $package_price_year = $package->price_year;
        
        if($request->type == "year"){ $package_price = $package->price_year; }

        $user_package = User_packageModel::where(["child_id"=> $request->child_id])->where('package_id','!=',0)->first();
        if($user_package){
            $order = Order_headModel::find($user_package->order_id);
            $type_order_date = 30;
            if($order->isType == "year"){ $type_order_date = 365; }
            $end_days = Helper::expire_package($user_package->end_date);
            $end_days_cal = $end_days;
            if(@$order->free_date > 0){
                $end_days_cal = $end_days - $order->free_date;
            }
            $remaining_price = floor($order->price / $type_order_date * $end_days_cal); // ยอดเงินของ Package เดิม
            if($request->upgrade_type == "upgrade"){ // อัพเกรด + วันใช้งาน 1 เดือน (30 วัน)
                $price = ceil(($package_price / $type_order_date * $end_days_cal) - $remaining_price); // ยอดเงินต้องจ่ายเพื่ออัพแพ็คเกจ
            }
            else if($request->upgrade_type == "monthly"){ // อัพเกรด + วันใช้งาน 1 เดือน (30 วัน)
                $price = ($package_price) - $remaining_price; // ยอดเงินต้องจ่ายเพื่ออัพแพ็คเกจ
                $end_days = 30;
            }
            else if($request->upgrade_type == "yearly"){ // อัพเกรด + วันใช้งาน 1 ปี (365 วัน)
                $price = ($package_price_year) - $remaining_price; // ยอดเงินต้องจ่ายเพื่ออัพแพ็คเกจ
                $end_days = 365;
            }

            if($price <= 0){ $price = 0; }
            $arr['status'] = 200;
            $arr['price'] = $price;
            $arr['priceshow'] = number_format($price,0);
            $arr['end_days'] = $end_days;
        }
        else{
            $arr['status'] = 500;
            $arr['message'] = "Not found";
        }
        return $arr;
    }

    public static function star($star){
        $data = "";
        
        $star_count = $star;
        for($i=0;$i<5;$i++)
        {
            $text = "";
            if($star_count > 0){
                $text = "text-warning";
            }
            $data.= '<i class="fa fa-star '.$text.'"></i>';
            $star_count = $star_count - 1;
        }
        return $data;
    }

    public static function star2($star){
        $data = "";
        
        $star_count = $star;
        for($i=0;$i<$star_count;$i++)
        {
            $data.= '<i class="fa-solid fa-star" style="color:#979797;"></i>';
        }
        return $data;
    }
    

    public static function topic($array){
        $array_text = ["A","B","C","D","E","F","G","H"];
        return $array_text[$array];
    }

    public static function status_quiz($status){
        $html = "";
        if($status == "W"){
            $html = '<small class="text-warning"><i class="uil-clock"></i> Waiting</small>';
        }elseif($status == "S"){
            $html = '<small class="complete-table"><i class="uil-check"></i> Complete</small>';
        }
        elseif($status == "R"){
            $html = '<small class="text-danger"><i class="uil-x"></i>Not passed</small>';
        }
        return $html;
    }

    public static function show_script($id,$type){
        $html = "";
        $data = Setting_scriptModel::find($id);
        if($data){
            if($type == "head"){ $html = $data->head; }
        }
        return $html;
    }

    public static function minute($start_date,$end_date){
        if($end_date != null){
            $date1 = new DateTime($start_date);
            $date2 = new DateTime($end_date);
    
            $differenceInSeconds = $date2->getTimestamp() - $date1->getTimestamp();
            $minutes = floor($differenceInSeconds / 60);
            $seconds = $differenceInSeconds % 60;
    
            if ($minutes > 0 && $seconds > 0) {
                return "$minutes นาที $seconds วินาที";
            } elseif ($minutes > 0) {
                return "$minutes นาที";
            } else {
                return "$seconds วินาที";
            }
        }else{
            return "-";
        }
    
    }
    public static function formatCompactNumber($number)
    {
        if ($number >= 1000000) {
            $value = round($number / 1000000, 1);
            return ($value == floor($value) ? number_format($value, 0) : number_format($value, 1)) . 'm';
        } elseif ($number >= 1000) {
            $value = round($number / 1000, 1);
            return ($value == floor($value) ? number_format($value, 0) : number_format($value, 1)) . 'k';
        } else {
            return number_format($number); // e.g. 857 stays 857
        }
    }
    //=====================
}