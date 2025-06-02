<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tbl_package';

    // Event listener for when a new item is being created
    public static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->created_by = Helper::cuby(Auth::guard('admin')->id());
        });

        static::updating(function ($item) {
            $item->updated_by = Helper::cuby(Auth::guard('admin')->id());
        });

        static::deleting(function ($item) {
            $item->updated_by = Helper::cuby(Auth::guard('admin')->id());
        });
    }

}
