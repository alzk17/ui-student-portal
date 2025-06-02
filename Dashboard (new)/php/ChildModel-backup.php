<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;

class ChildModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tbl_child';

    function fullname(){
        return $this->firstname.' '.$this->lastname;
    }

    
}
