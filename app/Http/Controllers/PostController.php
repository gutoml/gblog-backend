<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\Posts\PostStoreService;
use App\Services\Posts\PostUpdateService;
use App\Http\Requests\Posts\PostStoreRequest;
use App\Http\Requests\Posts\PostUpdateRequest;

class PostController extends Controller
{
    public function __construct(
        private Post $post
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->post
            ->with(['user', 'category'])
            ->latest()
            ->paginate(
                $request->has('per_page') ? $request->per_page : 20
            );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request): JsonResponse
    {
        try {
            $postStoreService = new PostStoreService();
            $response = $postStoreService->execute($request->validated());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return response()->json(
            $response,
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json(
            $post->load(['user', 'category', 'images', 'relatedPosts']),
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post): JsonResponse
    {
        try {
            $postUpdateService = new PostUpdateService($post);
            $response = $postUpdateService->execute($request->validated());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json(null, 204);
    }
}
