<?php

namespace App\Http\Controllers;

use App\Services\ExploreService;
use App\Services\SeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExploreController extends Controller
{
    protected ExploreService $exploreService;

    public function __construct(ExploreService $exploreService)
    {
        $this->exploreService = $exploreService;
    }

    /**
     * Display the continuous product discovery feed.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request): View|JsonResponse
    {
        // Stable session/query seed for controlled random order
        $seed = $request->filled('seed') ? (int) $request->input('seed') : random_int(100000, 999999);
        $page = max(1, (int) $request->input('page', 1));
        $perPage = max(8, min(32, (int) $request->input('per_page', 16)));

        $result = $this->exploreService->getProducts($seed, $page, $perPage);

        // Check if request is an infinite-scroll AJAX request
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            $html = view('explore._cards_list', [
                'products' => $result['products'],
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'page' => $result['page'],
                'next_page' => $result['page'] + 1,
                'has_more' => $result['has_more'],
                'cycle' => $result['cycle'],
                'count' => $result['products']->count(),
                'total' => $result['total'],
                'seed' => $seed,
            ]);
        }

        // Standard SSR response
        $seo = SeoService::forExplore();

        return view('explore.index', [
            'products' => $result['products'],
            'seed' => $seed,
            'page' => $result['page'],
            'perPage' => $perPage,
            'total' => $result['total'],
            'cycle' => $result['cycle'],
            'seo' => $seo,
        ]);
    }
}

