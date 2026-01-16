<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\LinkageController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\ProgramCourseController;
use App\Http\Controllers\Admin\ProgramBuilderController;
use App\Http\Controllers\Admin\LinkageCategoryController;
use App\Http\Controllers\Admin\CoursePrerequisiteController;
use App\Http\Controllers\Admin\ProgramRequirementController;
use App\Http\Controllers\Admin\ProgramRequirementCategoryController;

Route::get('/', function () {
    return view('homepage');
});

Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('dashboard');

// Route::prefix('admin')->name('admin.')->group(function () {

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class)->except(['show']);


    Route::resource('courses', CourseController::class);
    Route::resource('programs', ProgramController::class);
    Route::resource('program_requirement_categories', ProgramRequirementCategoryController::class);

    Route::get('programs/{program}/builder', [ProgramBuilderController::class, 'show'])
        ->name('programs.builder');


    // AJAX endpoints
    Route::post('programs/{program}/requirements/ajax', [ProgramBuilderController::class, 'storeRequirement'])
        ->name('programs.requirements.store.ajax');
    Route::delete('programs/{program}/requirements/{requirement}/ajax', [ProgramBuilderController::class, 'destroyRequirement'])
        ->name('programs.requirements.destroy.ajax');

    Route::post('programs/{program}/courses/ajax', [ProgramBuilderController::class, 'storeCourse'])
        ->name('programs.courses.store.ajax');
    Route::delete('programs/{program}/courses/{programCourse}/ajax', [ProgramBuilderController::class, 'destroyCourse'])
        ->name('programs.courses.destroy.ajax');



    // LINKAGE ROUTES
    Route::resource('linkage_categories', LinkageCategoryController::class);
    Route::resource('linkages', LinkageController::class);
    




    // AJAX routes for builder
    // Route::post(
    //     'programs/{program}/requirements/ajax',
    //     [ProgramRequirementController::class, 'store']
    // )->name('programs.requirements.store.ajax');

    // Route::post(
    //     'programs/{program}/courses/ajax',
    //     [ProgramCourseController::class, 'store']
    // )->name('programs.courses.store.ajax');


        
    // Route::get('programs/{program}/requirements', [ProgramRequirementController::class, 'index']);
    // Route::post('programs/{program}/requirements', [ProgramRequirementController::class, 'store']);
    // Route::delete('programs/{program}/requirements/{id}', [ProgramRequirementController::class, 'destroy']);

    // Route::get('programs/{program}/courses', [ProgramCourseController::class, 'index']);
    // Route::post('programs/{program}/courses', [ProgramCourseController::class, 'store']);
    // Route::delete('programs/{program}/courses/{id}', [ProgramCourseController::class, 'destroy']);



    // Route::get(
    //     'courses/{course}/prerequisites',
    //     [CoursePrerequisiteController::class, 'index']
    // )->name('courses.prerequisites.index');

    // Route::post(
    //     'courses/{course}/prerequisites',
    //     [CoursePrerequisiteController::class, 'store']
    // )->name('courses.prerequisites.store');

    // Route::delete(
    //     'courses/{course}/prerequisites/{prerequisite}',
    //     [CoursePrerequisiteController::class, 'destroy']
    // )->name('courses.prerequisites.destroy');

    
});

