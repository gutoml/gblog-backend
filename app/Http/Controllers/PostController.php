<?php

namespace App\Http\Controllers;

use App\Http\Requests\Posts\PostUpdateRequest;
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

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResponse
    {
        return response()->json($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post): JsonResponse
    {
        $post->update($request->validated());

        return response()->json($post);
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
