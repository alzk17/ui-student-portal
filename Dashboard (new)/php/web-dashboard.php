<?php 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend as Backend;
use App\Http\Controllers\Member as Member;
use App\Http\Controllers\Frontend as Frontend;


Route::get('/worksheet-selectsub', [Frontend\Child\DashboardController::class, 'worksheetSelectsub']);

// == Middleware
Route::group(['middleware' => ['User']], function () {
    Route::get('/create-child', [Frontend\SignupController::class, 'create_child']);
    Route::get('/packages_try', [Frontend\SignupController::class, 'packages_try']);
    Route::get('/payment', [Frontend\SignupController::class, 'payment']);
    Route::get('/complete', [Frontend\SignupController::class, 'complete']);
    Route::get('/check_first_child', [Frontend\SignupController::class, 'check_first_child']);
    Route::get('/checkchild-package', [Frontend\SignupController::class, 'checkchild_package']);

    Route::get('/mocktest-get', [Frontend\Mocktest_ajaxController::class, 'mocktestGet']);
    Route::get('/mocktest-subject-get', [Frontend\Mocktest_ajaxController::class, 'mocktestSubjectGet']);
    Route::get('/searchKeywordMocktestParent', [Frontend\Mocktest_ajaxController::class, 'searchKeywordMocktestParent']); // ค้นหาแบบคีย์เวิร์ด
    Route::post('/startmocktest', [Frontend\Mocktest_ajaxController::class, 'startMocktest']);
    
    
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [Frontend\DashboardController::class, 'index']);
        Route::get('/journeys', [Frontend\DashboardController::class, 'journeys']);
        Route::get('/review', [Frontend\DashboardController::class, 'review']);
        Route::get('/worksheet', [Frontend\DashboardController::class, 'worksheet']);

        Route::get('/transcript/{id}', [Frontend\DashboardController::class, 'transcript'])->where(['id' => '[0-9]+']);
        Route::get('/set-work', [Frontend\DashboardController::class, 'set_work']);
        Route::get('/topup', [Frontend\DashboardController::class, 'topup']);
        // == ตัวใหม่
        Route::get('/topup-new', [Frontend\DashboardController::class, 'topup_new']);
        Route::get('/topup-package', [Frontend\DashboardController::class, 'topup_package']);
        
        Route::get('/order', [Frontend\DashboardController::class, 'order']);
        
        // == Child
        Route::get('/create-child', [Frontend\ChildController::class, 'create_child']);
        Route::post('/create-child', [Frontend\ChildController::class, 'create_childSubmit']);
        Route::get('/child/{id}', [Frontend\ChildController::class, 'child'])->where(['id' => '[0-9]+']);
        Route::get('/child/edit/{id}', [Frontend\ChildController::class, 'child_edit'])->where(['id' => '[0-9]+']);
        Route::post('/child/edit/{id}', [Frontend\ChildController::class, 'child_editSubmit'])->where(['id' => '[0-9]+']);
        Route::get('/dashboard-child/{id}', [Frontend\ChildController::class, 'dashboard_child'])->where(['id' => '[0-9]+']);

        // Pratice
        Route::get('/list-practice', [Frontend\PracticeController::class, 'list_practice']);
        // Route::get('/practice-transcript/{id}', [Frontend\PracticeController::class, 'transcript_view'])->where(['id' => '[0-9A-Za-z\-]+']);
        Route::get('/list-practice/edit/{id}', [Frontend\PracticeController::class, 'edit_setwork'])->where(['id' => '[0-9]+']);
        Route::post('/list-practice/edit/{id}', [Frontend\PracticeController::class, 'update_setwork'])->where(['id' => '[0-9]+']);
        Route::get('/pratice-check-child', [Frontend\PracticeController::class, 'pratice_check_child']); // CHECK Package
        Route::post('/create-pratice', [Frontend\PracticeController::class, 'create_pratice']);
        Route::get('/practice-destroy/{id}', [Frontend\PracticeController::class, 'practice_destroy'])->where(['id' => '[0-9A-Za-z\-]+']);

        Route::get('/profile', [Frontend\DashboardController::class, 'profile']);
        Route::post('/profile', [Frontend\DashboardController::class, 'profileUpdate']);
        Route::get('logout', [Frontend\DashboardController::class, 'logOut']);
        Route::post('loginchild', [Frontend\SignupController::class, 'loginchild']);

        Route::get('/practice/review/{practice_id}', [Frontend\PracticeController::class, 'review'])->where(['practice_id' => '[0-9]+']);
        Route::get('/practice-transcript/{practice_id}', [Frontend\PracticeController::class, 'transcript'])->whereUuid(['practice_id']);

    });
});




// Child
Route::group(['middleware' => ['Child']], function () {

    
    Route::post('/pratice/check-answer', [Frontend\Child\PracticeController::class, 'check_answer']); // CHECK Package

    Route::prefix('dashboard-child')->group(function () {
        Route::get('/', [Frontend\Child\DashboardController::class, 'index']);
        
        Route::prefix('journey')->group(function(){
        Route::get('/', [Frontend\Child\DashboardController::class, 'journey']);
            Route::prefix('{journeyId}/{subjectId}')
                ->where(['journeyId'=> '[0-9]+','subjectId'=> '[0-9]+'])
                ->group(function(){
                    Route::get('/', [Frontend\Child\JourneyCtrl::class, 'index']);
                    Route::get('/get/lessons', [Frontend\Child\JourneyCtrl::class, 'getLessons']);
                    Route::get('/learning', [Frontend\Child\JourneyCtrl::class, 'learning']);
                    
                    // Actions that change data should be POST requests
                    Route::post('/reset', [Frontend\Child\JourneyCtrl::class, 'learningReset']); // Changed from GET to POST
                    Route::post('/set-latest', [Frontend\Child\JourneyCtrl::class, 'setLatest']);
                    Route::post('/learning/finished', [Frontend\Child\JourneyCtrl::class, 'finishedLearning']);
                    Route::post('/learning/answer', [Frontend\Child\JourneyCtrl::class, 'sendAnswer']);
                    
                    // Data retrieval can remain GET requests
                    Route::get('/examination/results', [Frontend\Child\JourneyCtrl::class, 'getExamination']);
                    Route::get('/learning/get', [Frontend\Child\JourneyLearningCtrl::class, 'get']);
                    Route::get('/learning/lessons', [Frontend\Child\JourneyLearningCtrl::class, 'getLessons']);
                    Route::get('/learning/lesson/{lessonId}', [Frontend\Child\JourneyLearningCtrl::class, 'getLesson'])->where('lessonId', '[0-9]+');
                    Route::get('/learning/lesson/{lessonId}/digest-content', [Frontend\Child\JourneyLearningCtrl::class, 'getDigestContent'])
                        ->where('lessonId', '[0-9]+');
                    Route::get('/learning/lesson/{lessonId}/application-content', [App\Http\Controllers\Frontend\Child\JourneyLearningCtrl::class, 'getApplicationContent'])->where('lessonId', '[0-9]+');
            });
        });

        
        Route::get('/review', [Frontend\Child\DashboardController::class, 'review']);
        Route::get('/quest', [Frontend\Child\DashboardController::class, 'quest']);
        Route::get('/badge-detail', [Frontend\Child\DashboardController::class, 'badge_detail']);
        Route::get('/worksheet', [Frontend\Child\DashboardController::class, 'worksheet']);
        Route::get('/hangouts', [Frontend\Child\DashboardController::class, 'hangouts']);



        Route::get('/set-work', [Frontend\Child\DashboardController::class, 'setwork']);

        Route::prefix('settings')->group(function () {
            // This creates the URL: /dashboard-child/settings/profile
            Route::get('/profile', [Frontend\Child\DashboardController::class, 'setting_profile'])->name('child.settings.profile');
            Route::post('/profile', [Frontend\Child\DashboardController::class, 'profileUpdate'])->name('child.settings.profile.update');
        });

        Route::get('/profile', [Frontend\Child\DashboardController::class, 'profile'])->name('child.profile.view');


        // Setwork
        Route::get('/check-subject', [Frontend\Child\SetworkController::class, 'check_subject']);
        Route::post('/create-pratice', [Frontend\Child\SetworkController::class, 'create_pratice']);

        
        // practice
        Route::get('/practice/{practice_id}', [Frontend\Child\PracticeController::class, 'index'])->whereUuid(['practice_id']);
        Route::get('/practice-transcript/{practice_id}', [Frontend\Child\PracticeController::class, 'transcript'])->whereUuid(['practice_id']);
        Route::get('/review/scorecard/{uuid}', [Frontend\Child\PracticeController::class, 'scorecard']);

        Route::get('/logout', [Frontend\Child\DashboardController::class, 'logOut']);
        Route::post('/parent/logout', [Frontend\Child\DashboardController::class, 'parentLogout']);
        Route::get('/parent/check', [Frontend\Child\DashboardController::class, 'parentCheck']);

        // Parent
        Route::get('/practice/review/{practice_id}', [Frontend\Child\PracticeController::class, 'review'])->where(['practice_id' => '[0-9]+']);
        
        Route::prefix("mocktest")->group(function(){
            Route::get('{mocktest_topic_id}', [Frontend\Child\MocktestController::class, 'index'])->where(['mocktest_topic_id' => '[0-9]+']);
            Route::get('review/{mocktest_topic_id}', [Frontend\Child\MocktestController::class, 'review'])->where(['mocktest_topic_id' => '[0-9]+']);
        });

        Route::post('/mocktest/answer', [Frontend\Child\MocktestController::class, 'answer']);
       
        

        //==Mocktest
        Route::get('/mocktest-get', [Frontend\Mocktest_ajaxController::class, 'mocktestGet_child']);
        Route::get('/mocktest-subject-get', [Frontend\Mocktest_ajaxController::class, 'mocktestSubjectGet_child']);
        Route::get('/searchKeywordMocktest', [Frontend\Mocktest_ajaxController::class, 'searchKeywordMocktest']); // ค้นหาแบบคีย์เวิร์ด

        Route::post('/startmocktest', [Frontend\Mocktest_ajaxController::class, 'startMocktest_child']);
        

        Route::post('/reportanswer', [Frontend\Child\FunctionController::class, 'reportanswer']);
        Route::post('/closeanswer', [Frontend\Child\FunctionController::class, 'closeanswer']);
        
        
    });
});

?>
