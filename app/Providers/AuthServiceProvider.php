<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Import your models and policies
use App\Models\Program;
use App\Models\Course;
use App\Models\ProgramRequirementCategory;
use App\Models\ProgramCourse;

use App\Policies\ProgramPolicy;
use App\Policies\CoursePolicy;
use App\Policies\ProgramRequirementCategoryPolicy;
use App\Policies\ProgramCoursePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Program::class => ProgramPolicy::class,
        Course::class => CoursePolicy::class,
        ProgramRequirementCategory::class => ProgramRequirementCategoryPolicy::class,
        ProgramCourse::class => ProgramCoursePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Optional: define custom gates
        Gate::define('admin-only', function ($user) {
            return $user->roles->contains('name', 'admin');
        });
    }
}
