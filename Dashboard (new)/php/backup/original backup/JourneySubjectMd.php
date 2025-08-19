<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JourneySubjectMd extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'tb_journey_subject';
    protected $primaryKey = 'id';
}
