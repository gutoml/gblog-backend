<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\PostHighline;
use Illuminate\Http\JsonResponse;
use App\Services\PostHighlines\PostHighlineStoreService;
use App\Services\PostHighlines\PostHighlineUpdateService;
use App\Http\Requests\PostHighlines\PostHighlineStoreRequest;
use App\Http\Requests\PostHighlines\PostHighlineUpdateRequest;

class PostHighlineController extends Controller
{
    public function __construct(
        private PostHighline $postHighline
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->postHighline->load('post')->latest()->get());
    }

    public function store(PostHighlineStoreRequest $request, PostHighlineStoreService $postHighlineStoreService): JsonResponse
    {
        try {
            $responseData = $postHighlineStoreService->execute($request->validated());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return response()->json($responseData, 201);
    }

    public function update(PostHighlineUpdateRequest $request, PostHighlineUpdateService $postHighlineUpdateService): JsonResponse
    {
        try {
            $responseData = $postHighlineUpdateService->execute($request->validated());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return response()->json($responseData);
    }

    public function destroy(PostHighline $postHighline): JsonResponse
    {
        $postHighline->delete();

        return response()->json(null, 204);
    }
}
