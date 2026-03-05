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



Route::get('/layout1', function () {
    return view('website.pages.news-and-announcements-layouts.layout1');
});
Route::get('/layout2', function () {
    return view('website.pages.news-and-announcements-layouts.layout2');
});
Route::get('/layout3', function () {
    return view('website.pages.news-and-announcements-layouts.layout3');
});
Route::get('/layout4', function () {
    return view('website.pages.news-and-announcements-layouts.layout4');
});
Route::get('/layout5', function () {
    return view('website.pages.news-and-announcements-layouts.layout5');
});




Route::get('/', [WebsiteController::class, 'home'])
    ->name('website.home');

Route::get('/about', function () {
    return view('website.pages.about');
})->name('website.pages.about');

Route::get('/online-payment', function () {
    return view('website.pages.admission.online-payment');
})->name('website.pages.online-payment');



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


// News and Announcement
Route::get(
    '/news-and-announcement',
    [WebsiteController::class, 'newsAndAnnouncement']
)
    ->name('website.news');

Route::get(
    '/news/load-more/{type}',
    [WebsiteController::class, 'loadMoreNews']
)
    ->name('news.load.more');

Route::get(
    '/news/{type}/{id}',
    [WebsiteController::class, 'showNews']
)
    ->where('type', 'announcement|dotuni') 
    ->where('id', '[0-9]+')
    ->name('news.show');









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
            Route::patch('users/{user}/archive', [UserController::class, 'archive'])->name('users.archive');
            Route::patch('users/{user}/unarchive', [UserController::class, 'unarchive'])->name('users.unarchive');
            Route::resource('users', UserController::class);
            
            Route::resource('roles', RoleController::class)->except(['show']);
        });

        // ============================
        // ADMIN + EDITOR + PUBLISHER 
        // ============================
        Route::middleware(['role:admin,editor,publisher'])->group(function () {
            Route::resource('dotuni_news', DotuniNewsController::class);
            Route::post(
                'dotuni_news/datatable',
                [DotuniNewsController::class, 'datatable']
            )->name('dotuni_news.datatable');
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
            Route::patch('courses/{course}/archive', [CourseController::class, 'archive'])->name('courses.archive');
            Route::patch('courses/{course}/unarchive', [CourseController::class, 'unarchive'])->name('courses.unarchive');


            Route::resource('programs', ProgramController::class);
            Route::patch('programs/{program}/archive', [ProgramController::class, 'archive'])->name('programs.archive');
            Route::patch('programs/{program}/unarchive', [ProgramController::class, 'unarchive'])->name('programs.unarchive');

            Route::patch('program_requirement_categories/{program_requirement_category}/archive', [ProgramRequirementCategoryController::class, 'archive'])->name('program_requirement_categories.archive');
            Route::patch('program_requirement_categories/{program_requirement_category}/unarchive', [ProgramRequirementCategoryController::class, 'unarchive'])->name('program_requirement_categories.unarchive');
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

            Route::patch('linkage_categories/{linkageCategory}/archive', [LinkageCategoryController::class, 'archive'])->name('linkage_categories.archive');
            Route::patch('linkage_categories/{linkageCategory}/unarchive', [LinkageCategoryController::class, 'unarchive'])->name('linkage_categories.unarchive');
            Route::resource('linkage_categories', LinkageCategoryController::class);

            Route::patch('linkages/{linkage}/archive', [LinkageController::class, 'archive'])->name('linkages.archive');
            Route::patch('linkages/{linkage}/unarchive', [LinkageController::class, 'unarchive'])->name('linkages.unarchive');
            Route::resource('linkages', LinkageController::class);
            
            Route::resource('clsu_news', ClsuNewsController::class);
            Route::patch('clsu-news/{clsuNews}/archive', [ClsuNewsController::class, 'archive'])->name('clsu_news.archive');
            Route::patch('clsu-news/{clsuNews}/unarchive', [ClsuNewsController::class, 'unarchive'])->name('clsu_news.unarchive');

            Route::resource('faqs_questions', FaqQuestionController::class);
            Route::patch('faqs-questions/{faqs_question}/archive', [FaqQuestionController::class, 'archive'])->name('faqs_questions.archive');
            Route::patch('faqs-questions/{faqs_question}/unarchive', [FaqQuestionController::class, 'unarchive'])->name('faqs_questions.unarchive');
            
            Route::resource('faqs_answers', FaqAnswerController::class);
            Route::patch('faqs-answers/{faqs_answer}/archive', [FaqAnswerController::class, 'archive'])->name('faqs_answers.archive');
            Route::patch('faqs-answers/{faqs_answer}/unarchive', [FaqAnswerController::class, 'unarchive'])->name('faqs_answers.unarchive');

            Route::patch('prospective_student_categories/{prospectiveStudentCategory}/archive', [ProspectiveStudentCategoryController::class, 'archive'])->name('prospective_student_categories.archive');
            Route::patch('prospective_student_categories/{prospectiveStudentCategory}/unarchive', [ProspectiveStudentCategoryController::class, 'unarchive'])->name('prospective_student_categories.unarchive');
            Route::resource('prospective_student_categories', ProspectiveStudentCategoryController::class);

            Route::patch('prospective_student_items/{prospectiveStudentItem}/archive', [ProspectiveStudentItemController::class, 'archive'])->name('prospective_student_items.archive');
            Route::patch('prospective_student_items/{prospectiveStudentItem}/unarchive', [ProspectiveStudentItemController::class, 'unarchive'])->name('prospective_student_items.unarchive');
            Route::resource('prospective_student_items', ProspectiveStudentItemController::class);

            Route::patch('rule_articles/{ruleArticle}/archive', [RuleArticleController::class, 'archive'])->name('rule_articles.archive');
            Route::patch('rule_articles/{ruleArticle}/unarchive', [RuleArticleController::class, 'unarchive'])->name('rule_articles.unarchive');
            Route::resource('rule_articles', RuleArticleController::class);

            Route::patch('rule_sections/{ruleSection}/archive', [RuleSectionController::class, 'archive'])->name('rule_sections.archive');
            Route::patch('rule_sections/{ruleSection}/unarchive', [RuleSectionController::class, 'unarchive'])->name('rule_sections.unarchive');
            Route::resource('rule_sections', RuleSectionController::class);

            Route::patch('rule_sub_sections/{ruleSubSection}/archive', [RuleSubSectionController::class, 'archive'])->name('rule_sub_sections.archive');
            Route::patch('rule_sub_sections/{ruleSubSection}/unarchive', [RuleSubSectionController::class, 'unarchive'])->name('rule_sub_sections.unarchive');
            Route::resource('rule_sub_sections', RuleSubSectionController::class);

            Route::patch('rule_clauses/{ruleClause}/archive', [RuleClauseController::class, 'archive'])->name('rule_clauses.archive');
            Route::patch('rule_clauses/{ruleClause}/unarchive', [RuleClauseController::class, 'unarchive'])->name('rule_clauses.unarchive');
            Route::resource('rule_clauses', RuleClauseController::class);

            Route::resource('form_categories', FormCategoryController::class);
            Route::patch('form-categories/{formCategory}/archive', [FormCategoryController::class, 'archive'])->name('form_categories.archive');
            Route::patch('form-categories/{formCategory}/unarchive', [FormCategoryController::class, 'unarchive'])->name('form_categories.unarchive');
            
            Route::resource('forms', FormController::class);
            Route::patch('forms/{form}/archive', [FormController::class, 'archive'])->name('forms.archive');
            Route::patch('forms/{form}/unarchive', [FormController::class, 'unarchive'])->name('forms.unarchive');

            Route::resource('e_resources', EResourceController::class);
            Route::patch('e-resources/{eResource}/archive', [EResourceController::class, 'archive'])->name('e_resources.archive');
            Route::patch('e-resources/{eResource}/unarchive', [EResourceController::class, 'unarchive'])->name('e_resources.unarchive');

            Route::resource('gallery', GalleryController::class);
            Route::patch('gallery/{gallery}/archive', [GalleryController::class, 'archive'])->name('gallery.archive');
            Route::patch('gallery/{gallery}/unarchive', [GalleryController::class, 'unarchive'])->name('gallery.unarchive');

            Route::resource('fees', FeeController::class);
            Route::patch('fees/{fee}/archive', [FeeController::class, 'archive'])->name('fees.archive');
            Route::patch('fees/{fee}/unarchive', [FeeController::class, 'unarchive'])->name('fees.unarchive');
            
        });

    });







