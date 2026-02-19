<?php

use App\Http\Controllers\Admin\academic\CourseController;

// gallery
use App\Http\Controllers\Admin\academic\ProgramBuilderController;

//  fee
use App\Http\Controllers\Admin\academic\ProgramController;

// user management
use App\Http\Controllers\Admin\academic\ProgramRequirementCategoryController;
use App\Http\Controllers\Admin\AdminController;

// clsu news
use App\Http\Controllers\Admin\AnnouncementController;

// faqs
use App\Http\Controllers\Admin\clsu\ClsuNewsController;

// rules and regulations
use App\Http\Controllers\Admin\DotuniNewsController;

// academic
use App\Http\Controllers\Admin\EResourceController;
use App\Http\Controllers\Admin\faqs\FaqAnswerController;
use App\Http\Controllers\Admin\faqs\FaqQuestionController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\form\FormCategoryController;
use App\Http\Controllers\Admin\form\FormController;

// linkage
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\linkage\LinkageCategoryController;

// prospective_student
use App\Http\Controllers\Admin\linkage\LinkageController;
use App\Http\Controllers\Admin\prospective_student\ProspectiveStudentCategoryController;

// forms
use App\Http\Controllers\Admin\prospective_student\ProspectiveStudentItemController;
use App\Http\Controllers\Admin\rule\RuleArticleController;

//EResource
use App\Http\Controllers\Admin\rule\RuleClauseController;
use App\Http\Controllers\Admin\rule\RuleSectionController;
// use App\Http\Controllers\Admin\academic\ProgramCourseController;
// use App\Http\Controllers\Admin\academic\ProgramRequirementController;

// DotUni News
use App\Http\Controllers\Admin\rule\RuleSubSectionController;

// announcements
use App\Http\Controllers\Admin\user\RoleController;
use App\Http\Controllers\Admin\user\UserController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Public Website Routes
// Route::get('/', function () {
//     return view('website.pages.home');
// })->name('website.pages.home');

// Route::get('/gallery', function () {
//     return view('website.pages.gallery');
// })->name('website.pages.gallery');

// Route::get('/courses', function () {
//     return view('website.pages.courses');
// })->name('website.pages.courses');

// Route::get('/admission-requirements', function () {
//     return view('website.pages.admission.admission-requirements');
// })->name('website.pages.admission-requirements');

// Route::get('/schedule-school-fees', function () {
//     return view('website.pages.admission.schedule-school-fees');
// })->name('website.pages.schedule-school-fees');

Route::get('/online-payment', function () {
    return view('website.pages.admission.online-payment');
})->name('website.pages.online-payment');

// Route::get('/faqs', function () {
//     return view('website.pages.admission.faqs');
// })->name('website.pages.faqs');

// Route::get('/rules-and-regulations', function () {
//     return view('website.pages.student-services.rules-and-regulations');
// })->name('website.pages.rules-and-regulations');

Route::get('/eresources', function () {
    return view('website.pages.student-services.eresources');
})->name('website.pages.eresources');











Route::get('/', [WebsiteController::class, 'home'])
    ->name('website.home');

Route::get('/about', function () {
    return view('website.pages.about');
})->name('website.pages.about');

Route::get('/gallery', [WebsiteController::class, 'gallery'])->name('website.gallery');
Route::get('/gallery/data', [WebsiteController::class, 'galleryData'])->name('website.galleryData');


// Academic Pages
Route::get('/courses', [WebsiteController::class, 'courses'])
    ->name('website.courses');

Route::get('/courses/data', [WebsiteController::class, 'coursesData'])
    ->name('website.coursesData');

Route::get('/courses/{program}', [WebsiteController::class, 'courseView'])
    ->name('website.course.view');




// Admission Pages
Route::get('/faqs', function () {
    return view('website.pages.admission.faqs');
})->name('website.faqs');

Route::get('/faqs/data', [WebsiteController::class, 'faqData'])
    ->name('website.faqData');

Route::get('/fees', [WebsiteController::class, 'fees'])
    ->name('website.fees');

Route::get('/admission-requirements', [WebsiteController::class, 'admissionRequirements'])
    ->name('website.admissionRequirements');


// Student Services Pages
Route::get('/rules-and-regulations', [WebsiteController::class, 'rulesAndRegulations'])
    ->name('website.rules-and-regulations');

Route::get('/e-resources', [WebsiteController::class, 'eResources'])
    ->name('website.eresources');


// Downlaods
Route::get('/downloads/{type}', [WebsiteController::class, 'downloads'])
    ->name('website.downloads');





// Authentication Routes
Auth::routes();

// Admin Routes
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

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

