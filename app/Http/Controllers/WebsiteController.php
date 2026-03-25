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
        // SECTION 1 — Homepage banners from gallery
        $banners = $this->galleryService->listBanners();

        // SECTION 2
        $linkages = $this->linkageService->list();

        // SECTION 3
        $news = $this->dotuniNewsService->list()
            ->where('status', 'published')
            ->sortByDesc('published_at');

        $mainNews = $news->take(2);
        $sideNews = $news->skip(2)->take(3);

        // SECTION 4 — ClsuNews: eager load thumbnail asset
        $clsuNews = $this->clsuNewsService->list()
            ->where('is_active', true)
            ->load('thumbnail')   // loads the BelongsTo Asset relation
            ->take(3);

        // SECTION 4 — Announcements: kept as raw models, assets already eager loaded by service
        $announcements = $this->announcementService->list()
            ->where('visibility', 'public')
            ->sortByDesc('publish_start')
            ->take(3);

        // SECTION 6
        $programs = $this->programService->list();

        // SECTION 7
        $faqs = $this->faqQuestionService->list()
            ->load(['answers' => fn($q) => $q->where('is_active', true)->whereNull('deleted_at')])
            ->take(5);

        return view('website.pages.home', compact(
            'banners',
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
        $perPage = 10;

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
            ->load([
                'items' => fn($q) => $q
                    ->where('is_active', true)
                    ->whereNull('deleted_at')
                    ->orderBy('sort_order')
            ]);

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


    public function newsAndAnnouncement()
    {
        // ================= ANNOUNCEMENTS =================
        $announcementQuery = $this->announcementService->list()
            ->where('visibility', 'public')
            ->sortByDesc('publish_start');

        $announcementTotal = $announcementQuery->count();

        $announcements = $announcementQuery
            ->take(8)
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->seo_description,
                    'image' => optional($item->thumbnail())->storage_path,
                    'date' => $item->publish_start ?? now(),
                    'type' => 'announcement',
                ];
            });


        // ================= DOTUNI =================
        $dotuniQuery = $this->dotuniNewsService->list()
            ->where('status', 'published')
            ->sortByDesc('published_at');

        $dotuniTotal = $dotuniQuery->count();

        $dotuniNews = $dotuniQuery
            ->take(8)
            ->map(function ($item) {
                $thumb = $item->attachments->where('is_thumbnail', true)->first();

                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->seo_description,
                    'image' => optional(optional($thumb)->asset)->storage_path,
                    'date' => $item->published_at ?? $item->created_at ?? now(),
                    'type' => 'dotuni',
                ];
            });


        // ================= CLSU =================
        $clsuQuery = $this->clsuNewsService->list()
            ->where('is_active', true)
            ->sortByDesc('created_at');

        $clsuTotal = $clsuQuery->count();

        $clsuNews = $clsuQuery
            ->take(8)
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'description' => $item->description,
                    'image' => optional($item->thumbnail)->storage_path,
                    'date' => $item->created_at ?? now(),
                    'type' => 'clsu',
                    'url' => $item->url,   // external redirect
                ];
            });

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
        $limit = 100;

        switch ($type) {

            // ================= ANNOUNCEMENT =================
            case 'announcement':
                $data = $this->announcementService->list()
                    ->where('visibility', 'public')
                    ->sortByDesc('publish_start')
                    ->slice(8, $limit)
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'description' => $item->seo_description,
                            'image' => optional($item->thumbnail())->storage_path,
                            'date' => $item->publish_start ?? now(),
                            'type' => 'announcement',
                        ];
                    });
                break;


            // ================= DOTUNI =================
            case 'dotuni':
                $data = $this->dotuniNewsService->list()
                    ->where('status', 'published')
                    ->sortByDesc('published_at')
                    ->slice(8, $limit)
                    ->map(function ($item) {
                        $thumb = $item->attachments->where('is_thumbnail', true)->first();

                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'description' => $item->seo_description,
                            'image' => optional(optional($thumb)->asset)->storage_path,
                            'date' => $item->published_at ?? $item->created_at ?? now(),
                            'type' => 'dotuni',
                        ];
                    });
                break;


            // ================= CLSU =================
            case 'clsu':
                $data = $this->clsuNewsService->list()
                    ->where('is_active', true)
                    ->sortByDesc('created_at')
                    ->slice(8, $limit)
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'title' => $item->title,
                            'description' => $item->description,
                            'image' => optional($item->thumbnail)->storage_path,
                            'date' => $item->created_at ?? now(),
                            'type' => 'clsu',
                            'url' => $item->url,
                        ];
                    });
                break;

            default:
                return response()->json([], 400);
        }

        return response()->json($data->values());
    }



public function showNews(string $type, int $id)
{
    switch ($type) {

        // ── ANNOUNCEMENT ──────────────────────────────────────────
        case 'announcement':
            $item = $this->announcementService
                ->list()
                ->where('visibility', 'public')
                ->firstWhere('id', $id);

            if (! $item) abort(404);

            // $item->assets = Collection<Asset> with pivot
            $normalizedAssets = $item->assets->map(function ($asset) {
                return (object) [
                    'storage_path' => $asset->storage_path,
                    'file_name'    => $asset->file_name,   // ← added
                    'kind'         => $asset->kind,
                    'caption'      => $asset->pivot->caption,
                    'is_thumbnail' => (bool) $asset->pivot->is_thumbnail,
                    'is_cover'     => (bool) $asset->pivot->is_cover,
                    'sort_order'   => $asset->pivot->sort_order,
                ];
            });

            $thumbnail = $normalizedAssets->firstWhere('is_thumbnail', true);

            $layoutKey  = str_replace('_', '', $item->layout ?? 'layout_1');
            $layoutView = "website.pages.news-and-announcements-layouts.{$layoutKey}";

            return view($layoutView, [
                'item' => [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'description' => $item->seo_description,
                    'body'        => $item->article_body,
                    'image'       => $thumbnail?->storage_path,
                    'assets'      => $normalizedAssets,
                    'date'        => $item->publish_start ?? $item->created_at,
                    'type'        => 'announcement',
                    'tags'        => [],
                ],
            ]);


        // ── DOTUNI ────────────────────────────────────────────────
        case 'dotuni':
            $item = $this->dotuniNewsService
                ->list()
                ->where('status', 'published')
                ->firstWhere('id', $id);

            if (! $item) abort(404);

            // $item->attachments = Collection<DotuniNewsAsset>
            $normalizedAssets = $item->attachments->map(function ($attachment) {
                $asset = $attachment->asset;
                return (object) [
                    'storage_path' => $asset?->storage_path,
                    'file_name'    => $asset?->file_name,  // ← added
                    'kind'         => $asset?->kind,
                    'caption'      => $attachment->caption,
                    'is_thumbnail' => (bool) $attachment->is_thumbnail,
                    'is_cover'     => (bool) $attachment->is_cover,
                    'sort_order'   => $attachment->sort_order,
                ];
            });

            $thumbnail = $normalizedAssets->firstWhere('is_thumbnail', true);

            $layoutKey  = str_replace('_', '', $item->layout ?? 'layout_1');
            $layoutView = "website.pages.news-and-announcements-layouts.{$layoutKey}";

            return view($layoutView, [
                'item' => [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'description' => $item->seo_description,
                    'body'        => $item->article_body,
                    'image'       => $thumbnail?->storage_path,
                    'assets'      => $normalizedAssets,
                    'date'        => $item->published_at ?? $item->created_at,
                    'type'        => 'dotuni',
                    'tags'        => [],
                ],
            ]);


        default:
            abort(404);
    }
}

    

}
