<?php

use App\Models\RuleSubSection;
use Illuminate\Support\Facades\Route;

// gallery
use App\Http\Controllers\Admin\GalleryController;

//  fee
use App\Http\Controllers\Admin\FeeController;

// user management
use App\Http\Controllers\Admin\user\RoleController;
use App\Http\Controllers\Admin\user\UserController;

// clsu news
use App\Http\Controllers\Admin\EResourceController;

// faqs
use App\Http\Controllers\Admin\form\FormController;

// rules and regulations
use App\Http\Controllers\Admin\DotuniNewsController;

// academic
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\clsu\ClsuNewsController;
use App\Http\Controllers\Admin\faqs\FaqAnswerController;
use App\Http\Controllers\Admin\academic\CourseController;
use App\Http\Controllers\Admin\linkage\LinkageController;
use App\Http\Controllers\Admin\rule\RuleClauseController;

// linkage
use App\Http\Controllers\Admin\academic\ProgramController;
use App\Http\Controllers\Admin\faqs\FaqQuestionController;

// prospective_student
use App\Http\Controllers\Admin\rule\RuleArticleController;
use App\Http\Controllers\Admin\rule\RuleSectionController;

// forms
use App\Http\Controllers\Admin\form\FormCategoryController;
use App\Http\Controllers\Admin\rule\RuleSubSectionController;

//EResource
use App\Http\Controllers\Admin\academic\ProgramBuilderController;
use App\Http\Controllers\Admin\linkage\LinkageCategoryController;
// use App\Http\Controllers\Admin\academic\ProgramCourseController;
// use App\Http\Controllers\Admin\academic\ProgramRequirementController;

// DotUni News
use App\Http\Controllers\Admin\academic\ProgramRequirementCategoryController;

// announcements
use App\Http\Controllers\Admin\prospective_student\ProspectiveStudentItemController;
use App\Http\Controllers\Admin\prospective_student\ProspectiveStudentCategoryController;


Route::get('/', function () {
    return view('homepage');
});

Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth'])
    ->group(function () {

        // ============================
        // ADMIN ONLY
        // ============================
        Route::middleware(['role:admin'])->group(function () {
            Route::resource('users', UserController::class);
            Route::resource('roles', RoleController::class)->except(['show']);
        });

        // ============================
        // ADMIN + EDITOR + PUBLISHER 
        // ============================
        Route::middleware(['role:admin,editor,publisher'])->group(function () {
            Route::resource('dotuni_news', DotuniNewsController::class);
            Route::resource('announcements', AnnouncementController::class);
        });

        // ============================
        // ADMIN + PUBLISHER 
        // ============================
        Route::middleware(['role:admin,publisher'])->group(function () {
            Route::patch('dotuni_news/{dotuniNews}/publish', [DotuniNewsController::class, 'publish'])
                ->name('dotuni_news.publish');
        });


        // ============================
        // ADMIN + EDITOR
        // ============================
        Route::middleware(['role:admin,editor'])->group(function () {

            Route::resource('courses', CourseController::class);
            Route::resource('programs', ProgramController::class);
            Route::resource('program_requirement_categories', ProgramRequirementCategoryController::class);

            Route::get('programs/{program}/builder', [ProgramBuilderController::class, 'show'])
                ->name('academic.programs.builder');

            Route::post('programs/{program}/requirements/ajax', [ProgramBuilderController::class, 'storeRequirement'])
                ->name('programs.requirements.store.ajax');

            Route::delete('programs/{program}/requirements/{requirement}/ajax', [ProgramBuilderController::class, 'destroyRequirement'])
                ->name('programs.requirements.destroy.ajax');

            Route::post('programs/{program}/courses/ajax', [ProgramBuilderController::class, 'storeCourse'])
                ->name('programs.courses.store.ajax');

            Route::delete('programs/{program}/courses/{programCourse}/ajax', [ProgramBuilderController::class, 'destroyCourse'])
                ->name('programs.courses.destroy.ajax');

            Route::resource('linkage_categories', LinkageCategoryController::class);
            Route::resource('linkages', LinkageController::class);
            Route::resource('clsu_news', ClsuNewsController::class);
            Route::resource('faqs_questions', FaqQuestionController::class);
            Route::resource('faqs_answers', FaqAnswerController::class);
            Route::resource('prospective_student_categories', ProspectiveStudentCategoryController::class);
            Route::resource('prospective_student_items', ProspectiveStudentItemController::class);
            Route::resource('rule_articles', RuleArticleController::class);
            Route::resource('rule_sections', RuleSectionController::class);
            Route::resource('rule_sub_sections', RuleSubSectionController::class);
            Route::resource('rule_clauses', RuleClauseController::class);
            Route::resource('form_categories', FormCategoryController::class);
            Route::resource('forms', FormController::class);
            Route::resource('e_resources', EResourceController::class);
            Route::resource('gallery', GalleryController::class);
            Route::resource('fees', FeeController::class);
            
        });

    });


// Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

//     Route::resource('users', UserController::class);
//     Route::resource('roles', RoleController::class)->except(['show']);


//     Route::resource('courses', CourseController::class);
//     Route::resource('programs', ProgramController::class);
//     Route::resource('program_requirement_categories', ProgramRequirementCategoryController::class);


//     Route::get('programs/{program}/builder', [ProgramBuilderController::class, 'show'])
//         ->name('academic.programs.builder');

//     // AJAX endpoints
//     Route::post('programs/{program}/requirements/ajax', [ProgramBuilderController::class, 'storeRequirement'])
//         ->name('programs.requirements.store.ajax');
//     Route::delete('programs/{program}/requirements/{requirement}/ajax', [ProgramBuilderController::class, 'destroyRequirement'])
//         ->name('programs.requirements.destroy.ajax');

//     Route::post('programs/{program}/courses/ajax', [ProgramBuilderController::class, 'storeCourse'])
//         ->name('programs.courses.store.ajax');
//     Route::delete('programs/{program}/courses/{programCourse}/ajax', [ProgramBuilderController::class, 'destroyCourse'])
//         ->name('programs.courses.destroy.ajax');



//     // LINKAGE ROUTES
//     Route::resource('linkage_categories', LinkageCategoryController::class);
//     Route::resource('linkages', LinkageController::class);


//     // CLSU NEWS ROUTES
//     Route::resource('clsu_news', ClsuNewsController::class);

//     // FAQS ROUTES
//     Route::resource('faqs_questions', FaqQuestionController::class);
//     Route::resource('faqs_answers', FaqAnswerController::class);

//     // Information Prospective Students Routes
//     Route::resource('prospective_student_categories', ProspectiveStudentCategoryController::class);
//     Route::resource('prospective_student_items', ProspectiveStudentItemController::class);


//     // Rules and Regulations Routes
//     Route::resource('rule_articles', RuleArticleController::class);
//     Route::resource('rule_sections', RuleSectionController::class);
//     Route::resource('rule_sub_sections', RuleSubSectionController::class);
//     Route::resource('rule_clauses', RuleClauseController::class);

//     // Forms Routes
//     Route::resource('form_categories', FormCategoryController::class);
//     Route::resource('forms', FormController::class);

//     // E-Resources Routes
//     Route::resource('e_resources', EResourceController::class);

//     // Gallery Routes
//     Route::resource('gallery', GalleryController::class);

//     // DotUni News Routes
//     Route::resource('dotuni_news', DotuniNewsController::class);

//     // Announcements Routes
//     Route::resource('announcements', AnnouncementController::class);

//     // Fee Routes
//     Route::resource('fees', FeeController::class);

// });


// Route::fallback('/error404', function () {
//     return view('error_404');
// });