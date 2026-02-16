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

class WebsiteController extends Controller
{
    protected $linkageService;
    protected $dotuniNewsService;
    protected $clsuNewsService;
    protected $announcementService;
    protected $programService;
    protected $faqQuestionService;
    protected $galleryService;

    public function __construct(
        LinkageService $linkageService,
        DotuniNewsService $dotuniNewsService,
        ClsuNewsService $clsuNewsService,
        AnnouncementService $announcementService,
        ProgramService $programService,
        FaqQuestionService $faqQuestionService,

        GalleryService $galleryService,
    ) {
        $this->linkageService = $linkageService;
        $this->dotuniNewsService = $dotuniNewsService;
        $this->clsuNewsService = $clsuNewsService;
        $this->announcementService = $announcementService;
        $this->programService = $programService;
        $this->faqQuestionService = $faqQuestionService;

        $this->galleryService = $galleryService;
    }

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
            ->take(5);

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

}
