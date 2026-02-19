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



    // Courses page
    public function courses()
    {
        return view('website.pages.courses');
    }

    public function coursesData(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = 8; // adjust depending on design

        $courses = $this->programService->listPaginated($page, $perPage);

        return response()->json($courses);
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



}
