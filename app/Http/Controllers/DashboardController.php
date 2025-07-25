<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = $request->get('perPage', 5); // Padrão de 5 itens por página

        // Otimizando as consultas
        $responseData = [
            'stats' => [
                'posts_count' => $user->posts()->count(),
                'categories_count' => Category::has('posts')->count(),
            ],

            'categories_ranking' => Category::select(['id', 'name', 'slug'])
                ->withCount('posts')
                ->orderByDesc('posts_count')
                ->limit(10)
                ->get(),

            'latest_posts' => Post::query()
                ->select(['id', 'title', 'slug', 'created_at', 'user_id', 'category_id'])
                ->with([
                    'user:id,name',
                    'category:id,name,slug'
                ])
                ->orderByDesc('created_at')
                ->paginate($perPage)
        ];

        return response()->json($responseData);
    }
}
