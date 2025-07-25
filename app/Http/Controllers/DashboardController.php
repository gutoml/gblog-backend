<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->has('perPage')
            ? $request->get('perPage')
            : 15;

        $responseData = [
            'postsCount' => $user->posts()->count(),
            'categoriesRanking' => Category::withCount('posts')
                ->orderByDesc('posts_count')
                ->limit(10)
                ->get(),
            'postsLast' => Post::with(['user', 'category'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
        ];

        return response()->json($responseData);
    }
}
