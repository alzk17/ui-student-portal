<?php

namespace App\Models\Backend; // Corrected namespace

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JourneyLearningMd extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'tb_journey_learning';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'journeyId',
        'subjectId',
        'userId',
    ];
}