<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;

class JourneyLessonCompletionMd extends Model
{
    protected $table = 'tb_journey_lesson_completions';

    protected $fillable = [
        'user_id',
        'lesson_id',
    ];
}