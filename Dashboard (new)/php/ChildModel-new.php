<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use App\Models\Backend\PackageModel;
use Carbon\Carbon;

class ChildModel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_child';

    // Relationship to PackageModel (via package_id in tbl_child)
    public function package()
    {
        return $this->hasOne(PackageModel::class, 'id', 'package_id');
    }

    // Accessor for full name
    public function fullname()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    // Accessor for age
    public function getAgeAttribute()
    {
        return $this->birthdate ? Carbon::parse($this->birthdate)->age : null;
    }

    // Accessor for plan name
    public function getPlanNameAttribute()
    {
        return $this->package?->name ?? 'No Plan';
    }
}
