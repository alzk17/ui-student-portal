<?php
/**
 * NOTE:
 * 
 * In this system:
 * - "Subject" refers to what we call a "Topic" elsewhere.
 * - Quiz content is embedded inside digest pages (reading and understanding material).
 * - Practice questions are part of the application section (interactive exercises).
 * 
 * Although treated similarly for tracking user progress, quizzes and practices 
 * serve different pedagogical purposes and reside in different parts of the lesson flow.
 */
namespace App\Http\Controllers\Frontend\Child;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Backend\JourneyLearningMd;
use App\Models\Backend\JourneyLessonMd;
use App\Models\Backend\JourneyLessonSectionMd;
use App\Models\Backend\JourneyPracticeMd;

class JourneyLearningCtrl extends Controller
{

    public function user(){
        return Auth::guard('child')->user();
    }

    // Get user learning progress data
    public function get(Request $request, $journeyId=null, $subjectId=null)
    {
        $user = self::user();
        // CORRECTED: Uses camelCase column names to match the tb_journey_learning table
        $get = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId' => $user->id
        ])->first();

        if (@$get->id) {
            // Existing record found - return 200 OK
            return response()->json($get, 200);
        } else {
            // No record found - create new and return 201 Created
            $data = new JourneyLearningMd;
            // CORRECTED: Uses camelCase properties to match the database
            $data->journeyId = $journeyId;
            $data->subjectId = $subjectId;
            $data->userId = $user->id;
            if ($data->save()) {
                return response()->json($data, 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to create new learning progress record.'
                ], 500);
            }
        }
    }

    // Get all lessons for a subject, ordered by list_order
    public function getLessons($journeyId = null, $subjectId = null)
    {
        try {
            // Each lesson in a lesson map must have the same journey_id and journey_subject_id.
            // Navigation and progress tracking depend on this consistency.
            // NO CHANGE NEEDED: This function correctly uses snake_case for the tb_journey_subject_lessons table
            $lessons = JourneyLessonMd::where([
                'journey_id' => $journeyId,
                'journey_subject_id' => $subjectId
            ])
            ->orderBy('list_order', 'asc')
            ->get();

            return response()->json($lessons);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Get lesson content and related practices for Vue lesson page
    public function getLesson($journeyId = null, $subjectId = null, $lessonId = null)
    {
        try {
            $lesson = JourneyLessonMd::findOrFail($lessonId);

            $practice = JourneyPracticeMd::where([
                'subject_id' => $subjectId,
                'lesson_id' => $lessonId
            ])->get();

            return response()->json([
                'lesson' => $lesson,
                'practice' => $practice
            ]);
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update the latest lesson user has viewed (progress tracking)
    public function setLatest(Request $request, $journeyId=null, $subjectId=null)
    {
        $user = self::user();
        // CORRECTED: Uses camelCase column names to match the tb_journey_learning table
        $data = JourneyLearningMd::where([
            'journeyId' => $journeyId,
            'subjectId' => $subjectId,
            'userId' => $user->id
        ])->first();

        if(@$data->id){
            $lesson = JourneyLessonMd::where([
                'id' => $request->lessonId,
                'journey_id' => $journeyId,
                'journey_subject_id' => $subjectId
            ])->first();

            if (!$lesson) {
                return response()->json([
                    'status' => false,
                    'statusText' => 'Invalid lesson for this journey and subject.',
                ], 400);
            }

            $data->latest = $request->lessonId;
            if($data->save()){
                return response()->json([
                    'status' => true,
                    'statusText' => 'The data has been updated.',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'statusText' => 'Oops, an error occurred.',
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'statusText' => 'No data found.',
            ], 404);
        }
    }
    
    public function getDigestContent($journeyId = null, $subjectId = null, $lessonId = null)
    {
        try {
            // Fetch the main lesson (first page)
            $lesson = JourneyLessonMd::findOrFail($lessonId);

            // Fetch subsequent pages/sections for the lesson
            // NO CHANGE NEEDED: This function correctly uses snake_case for the tb_journey_subject_lessons_section table
            $sections = JourneyLessonSectionMd::where('journey_lesson_id', $lessonId)
                ->orderBy('list_order', 'asc')
                ->get();

            // Prepare combined pages array starting with main lesson content
            $pages = [];

            $pages[] = [
                'id' => $lesson->id,
                'type' => 'content',
                'title' => $lesson->name,
                'content' => $lesson->content,
                'list_order' => 0,  // main page first
            ];

            foreach ($sections as $section) {
                $pages[] = [
                    'id' => $section->id,
                    'type' => 'content',
                    'title' => null,
                    'content' => $section->detail,
                    'list_order' => $section->list_order,
                ];
            }

            // Sort pages by list_order just in case
            usort($pages, function ($a, $b) {
                return ($a['list_order'] ?? 0) <=> ($b['list_order'] ?? 0);
            });

            // Return combined response
            return response()->json([
                'lesson' => [
                    'id' => $lesson->id,
                    'name' => $lesson->name,
                    'journey_id' => $lesson->journey_id ?? null,
                    'journey_subject_id' => $lesson->journey_subject_id ?? null,
                ],
                'pages' => $pages,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}