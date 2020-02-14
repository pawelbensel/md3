<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SimilarResource;
use App\Http\Resources\SimilarsCollection;
use App\Models\Similar;
use App\Services\Merge\MergeServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimilarsController extends Controller
{
    private $mergeService;

    public function __construct(MergeServiceInterface $mergeService)
    {
        $this->mergeService = $mergeService;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function merge(Request $request, $id)
    {
        $similar = Similar::findOrFail($id);
        $mergeHistory = $this->mergeService->mergre($similar);

        return new JsonResponse($mergeHistory, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function revert(Request $request, $id)
    {
        $similar = Similar::withTrashed()->findOrFail($id);
        $revert = $this->mergeService->revert($similar);

        return new JsonResponse('ok', 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function discard(Request $request, $id)
    {
        $similar = Similar::findOrFail($id);
        $this->mergeService->discard($similar);

        return new JsonResponse('ok', 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->get('page')?? 0;
        $similars = $this->mergeService->getSimilars($page);

        return new JsonResponse(new SimilarsCollection($similars), 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function one(Request $request, $id)
    {
        $similar = Similar::findOrFail($id);
        return new JsonResponse(new SimilarResource($similar), 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function history(Request $request)
    {
        $page = $request->get('page')?? 0;
        $history = $this->mergeService->getHistory($page);

        return new JsonResponse(new SimilarsCollection($history), 200);
    }
}
