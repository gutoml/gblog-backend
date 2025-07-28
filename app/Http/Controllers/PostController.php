<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\PostUpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Posts\PostStoreRequest;

class PostController extends Controller
{
    public function __construct(
        private Post $post
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->post->all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request): JsonResponse
    {
        $post = $this->post->create($request->validated());
        $post->images()->attach($request->image_id);

        return response()->json(
            new PostResource(
                $post->load(['category', 'images'])
            ),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json(
            new PostResource(
                $post->load(['category', 'images'])
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post): JsonResponse
    {
        $post->update($request->validated());
        $post->images()->sync($request->image_id);

        return response()->json(new PostResource($post->load(['category', 'images'])));
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
