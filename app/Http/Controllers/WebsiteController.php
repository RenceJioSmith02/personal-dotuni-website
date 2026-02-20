<?php

namespace App\Http\Controllers;

use App\Services\Academic\ProgramService;
use App\Services\AnnouncementService;
use App\Services\Clsu\ClsuNewsService;
use App\Services\DotuniNewsService;
use App\Services\Faqs\FaqQuestionService;
use App\Services\GalleryService;
use App\Services\Linkage\LinkageService;
use Illuminate\Http\Request;
use App\Services\FeeService;
use App\Services\ProspectiveStudent\ProspectiveStudentCategoryService;

use App\Services\Rule\RuleWebsiteService;
use App\Services\EResourceService;

use App\Services\Form\FormCategoryService;

use App\Services\Academic\ProgramBuilderService;
use App\Models\Program;







class WebsiteController extends Controller
{
    protected $linkageService;
    protected $dotuniNewsService;
    protected $clsuNewsService;
    protected $announcementService;
    protected $programService;
    protected $faqQuestionService;
    protected $galleryService;
    protected $feeService;
    protected $prospectiveStudentCategoryService;
    protected $ruleWebsiteService;
    protected $eResourceService;
    protected $formCategoryService;
    protected $programBuilderService;





    public function __construct(
        LinkageService $linkageService,
        DotuniNewsService $dotuniNewsService,
        ClsuNewsService $clsuNewsService,
        AnnouncementService $announcementService,
        ProgramService $programService,
        FaqQuestionService $faqQuestionService,

        GalleryService $galleryService,
        FeeService $feeService,
        ProspectiveStudentCategoryService $prospectiveStudentCategoryService,

        RuleWebsiteService $ruleWebsiteService,
        EResourceService $eResourceService,

        FormCategoryService $formCategoryService,

        ProgramBuilderService $programBuilderService,


    ) {
        $this->linkageService = $linkageService;
        $this->dotuniNewsService = $dotuniNewsService;
        $this->clsuNewsService = $clsuNewsService;
        $this->announcementService = $announcementService;
        $this->programService = $programService;
        $this->faqQuestionService = $faqQuestionService;

        $this->galleryService = $galleryService;
        $this->feeService = $feeService;
        $this->prospectiveStudentCategoryService = $prospectiveStudentCategoryService;

        $this->ruleWebsiteService = $ruleWebsiteService;
        $this->eResourceService = $eResourceService;

        $this->formCategoryService = $formCategoryService;

        $this->programBuilderService = $programBuilderService;




    }

    // Home page
    public function home()
    {
        // SECTION 2
        $linkages = $this->linkageService->list();

        // SECTION 3
        $news = $this->dotuniNewsService->list()
            ->where('status', 'published')
            ->sortByDesc('published_at');

        $mainNews = $news->take(1);
        $sideNews = $news->skip(1)->take(3);

        // SECTION 4
        $clsuNews = $this->clsuNewsService->list()->take(3); 

        $announcements = $this->announcementService->list()
            ->take(3);

        // SECTION 6
        $programs = $this->programService->list();

        // SECTION 7
        $faqs = $this->faqQuestionService->list()
            ->load(['answers:id,faq_id,answer'])
            ->take(5);

        // $faqs = $this->faqQuestionService->list()
        //     ->take(5);

        return view('website.pages.home', compact(
            'linkages',
            'mainNews',
            'sideNews',
            'clsuNews',
            'announcements',
            'programs',
            'faqs'
        ));
    }


    // Gallery page
    public function gallery()
    {
        return view('website.pages.gallery');
    }

    // AJAX: Get paginated gallery data
    public function galleryData(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 20; // 5 columns x 4 rows

        $gallery = $this->galleryService->listPaginated($page, $perPage);

        return response()->json($gallery);
    }



    // Academic Pages
    public function courses()
    {
        return view('website.pages.course.courses');
    }

    public function coursesData(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 8; // adjust depending on design

        $courses = $this->programService->listPaginated($page, $perPage);

        return response()->json($courses);
    }

    public function courseView(Program $program)
    {
        if (!$program->is_active) {
            abort(404);
        }

        $program = $this->programBuilderService
            ->loadProgram($program);

        return view('website.pages.course.course-view', compact('program'));
    }




    // Admission Pages
    public function faqData(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 2; // you can adjust

        $faqs = $this->faqQuestionService
            ->listPaginated($page, $perPage);

        return response()->json($faqs);
    }

    public function fees()
    {
        $fees = $this->feeService->list();

        return view('website.pages.admission.fees', compact('fees'));
    }

    public function admissionRequirements()
    {
        $categories = $this->prospectiveStudentCategoryService
            ->list()
            ->load('items'); // eager load items for each category

        return view('website.pages.admission.requirements', compact('categories'));
    }


    // Student Services Pages
    public function rulesAndRegulations()
    {
        $articles = $this->ruleWebsiteService->list();

        return view('website.pages.student-services.rules-and-regulations', compact('articles'));
    }

    public function eResources()
    {
        $resources = $this->eResourceService->websiteList();

        return view('website.pages.student-services.eresources', compact('resources'));
    }


    // Downloads
    public function downloads($type)
    {
        $categories = $this->formCategoryService
            ->list()
            ->load(['forms.asset']);

        // ONLY Course Prospectus
        if ($type === 'course-prospectus') {

            $categories = $categories->where('slug', 'course-prospectus');

        }
        // EVERYTHING EXCEPT Course Prospectus
        else {

            $categories = $categories->where('slug', '!=', 'course-prospectus');

        }

        return view('website.pages.downloads', compact('categories', 'type'));
    }



    // public function newsAndAnnouncement()
    // {

    //     // ANNOUNCEMENTS
    //     $announcementQuery = $this->announcementService->list()
    //         ->where('visibility', 'public');

    //     $announcementTotal = $announcementQuery->count();

    //     $announcements = $announcementQuery
    //         ->take(8)
    //         ->map(function ($item) {

    //             $thumbnail = $item->thumbnail();

    //             return [
    //                 'title' => $item->title,
    //                 'description' => $item->seo_description,
    //                 'image' => optional($thumbnail)->storage_path,
    //                 'date' => $item->publish_start,
    //                 'type' => 'announcement'
    //             ];
    //         });



    //     // DOTUNI
    //     $dotuniQuery = $this->dotuniNewsService->list()
    //         ->where('status', 'published');

    //     $dotuniTotal = $dotuniQuery->count();

    //     $dotuniNews = $dotuniQuery
    //         ->take(8)
    //         ->map(function ($item) {

    //             $thumb = $item->attachments
    //                 ->where('is_thumbnail', true)
    //                 ->first();

    //             return [
    //                 'title' => $item->title,
    //                 'description' => $item->seo_description,
    //                 'image' => optional(optional($thumb)->asset)->storage_path,
    //                 'date' => $item->published_at,
    //                 'type' => 'dotuni'
    //             ];
    //         });



    //     // CLSU
    //     $clsuQuery = $this->clsuNewsService->list()
    //         ->where('is_active', true);

    //     $clsuTotal = $clsuQuery->count();

    //     $clsuNews = $clsuQuery
    //         ->take(8)
    //         ->map(function ($item) {

    //             return [
    //                 'title' => $item->title,
    //                 'description' => $item->description,
    //                 'image' => $item->imagePath,
    //                 'date' => $item->created_at,
    //                 'type' => 'clsu'
    //             ];
    //         });


    //     return view('website.pages.news-and-announcement', [
    //         'announcements' => $announcements,
    //         'dotuniNews' => $dotuniNews,
    //         'clsuNews' => $clsuNews,

    //         'announcementTotal' => $announcementTotal,
    //         'dotuniTotal' => $dotuniTotal,
    //         'clsuTotal' => $clsuTotal,
    //     ]);
    // }


    // public function loadMoreNews($type)
    // {

    //     if ($type == 'announcement') {
    //         $data = $this->announcementService->list()
    //             ->where('visibility', 'public')
    //             ->skip(8);
    //     }

    //     if ($type == 'dotuni') {
    //         $data = $this->dotuniNewsService->list()
    //             ->where('status', 'published')
    //             ->skip(8);
    //     }

    //     if ($type == 'clsu') {
    //         $data = $this->clsuNewsService->list()
    //             ->where('is_active', true)
    //             ->skip(8);
    //     }

    //     $data = $data->map(function ($item) use ($type) {

    //         if ($type == 'announcement') {
    //             $thumb = $item->thumbnail();
    //             $img = optional($thumb)->storage_path;
    //             $desc = $item->seo_description;
    //             $date = $item->publish_start;
    //         }

    //         if ($type == 'dotuni') {
    //             $thumb = $item->attachments
    //                 ->where('is_thumbnail', true)
    //                 ->first();
    //             $img = optional(optional($thumb)->asset)->storage_path;
    //             $desc = $item->seo_description;
    //             $date = $item->published_at;
    //         }

    //         if ($type == 'clsu') {
    //             $img = $item->imagePath;
    //             $desc = $item->description;
    //             $date = $item->created_at;
    //         }

    //         return view('website.partials.news-card', [
    //             'content' => [
    //                 'title' => $item->title,
    //                 'description' => $desc,
    //                 'image' => $img,
    //                 'date' => $date,
    //                 'type' => $type
    //             ]
    //         ])->render();

    //     });

    //     return response()->json($data->values());
    // }




    public function newsAndAnnouncement()
    {
        // ANNOUNCEMENTS
        $announcementQuery = $this->announcementService->list()
            ->where('visibility', 'public');

        $announcementTotal = $announcementQuery->count();

        $announcements = $announcementQuery
            ->map(function ($item) {
                return [
                    'title' => $item->title,
                    'description' => $item->seo_description,
                    'image' => optional($item->thumbnail())->storage_path,
                    'date' => $item->publish_start, // use publish_start for sorting
                    'type' => 'announcement'
                ];
            })
            ->sortByDesc('date')  // sort by publish_start descending
            ->take(8);

        // DOTUNI
        $dotuniQuery = $this->dotuniNewsService->list()
            ->where('status', 'published');

        $dotuniTotal = $dotuniQuery->count();

        $dotuniNews = $dotuniQuery
            ->map(function ($item) {
                $thumb = $item->attachments->where('is_thumbnail', true)->first();
                return [
                    'title' => $item->title,
                    'description' => $item->seo_description,
                    'image' => optional(optional($thumb)->asset)->storage_path,
                    'date' => $item->published_at,
                    'type' => 'dotuni'
                ];
            })
            ->sortByDesc('date')  // sort by published_at
            ->take(8);

        // CLSU
        $clsuQuery = $this->clsuNewsService->list()
            ->where('is_active', true);

        $clsuTotal = $clsuQuery->count();

        $clsuNews = $clsuQuery
            ->map(function ($item) {
                return [
                    'title' => $item->title,
                    'description' => $item->description,
                    'image' => $item->imagePath,
                    'date' => $item->created_at,
                    'type' => 'clsu'
                ];
            })
            ->sortByDesc('date')  // sort by created_at
            ->take(8);

        return view('website.pages.news-and-announcement', [
            'announcements' => $announcements,
            'dotuniNews' => $dotuniNews,
            'clsuNews' => $clsuNews,
            'announcementTotal' => $announcementTotal,
            'dotuniTotal' => $dotuniTotal,
            'clsuTotal' => $clsuTotal,
        ]);
    }

    public function loadMoreNews($type)
    {
        if ($type == 'announcement') {
            $data = $this->announcementService->list()
                ->where('visibility', 'public')
                ->map(function ($item) {
                    $thumb = $item->thumbnail();
                    return [
                        'title' => $item->title,
                        'description' => $item->seo_description,
                        'image' => optional($thumb)->storage_path,
                        'date' => $item->publish_start,
                        'type' => 'announcement'
                    ];
                })
                ->sortByDesc('date')   // sort first
                ->slice(8);            // then skip 8
        }

        if ($type == 'dotuni') {
            $data = $this->dotuniNewsService->list()
                ->where('status', 'published')
                ->map(function ($item) {
                    $thumb = $item->attachments->where('is_thumbnail', true)->first();
                    return [
                        'title' => $item->title,
                        'description' => $item->seo_description,
                        'image' => optional(optional($thumb)->asset)->storage_path,
                        'date' => $item->published_at,
                        'type' => 'dotuni'
                    ];
                })
                ->sortByDesc('date')
                ->slice(8);
        }

        if ($type == 'clsu') {
            $data = $this->clsuNewsService->list()
                ->where('is_active', true)
                ->map(function ($item) {
                    return [
                        'title' => $item->title,
                        'description' => $item->description,
                        'image' => $item->imagePath,
                        'date' => $item->created_at,
                        'type' => 'clsu'
                    ];
                })
                ->sortByDesc('date')
                ->slice(8);
        }

        // render news cards
        $rendered = $data->map(function ($item) {
            return view('website.partials.news-card', [
                'content' => $item
            ])->render();
        });

        return response()->json($rendered->values());
    }











}
