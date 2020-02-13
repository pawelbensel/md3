<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SimilarResource;
use App\Http\Resources\SimilarsCollection;
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

    public function index(Request $request)
    {
        $page = $request->get('page')?? 0;
        $similars = $this->mergeService->getSimilars($page);

        return new JsonResponse(new SimilarsCollection($similars), 200);
    }
}
