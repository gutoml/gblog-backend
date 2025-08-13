<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\PostView;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Database\Query\Builder;

class TrackPostView
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->route('post') instanceof Post) {
            $post = $request->route('post');

            $viewExists = PostView::where('post_id', $post->id)
                ->when(auth()->check(), function (Builder $query) {
                    $query->where('user_id', auth()->id());
                }, function (Builder $query) use ($request) {
                    $query->where('ip_address', $request->ip());
                })
                ->exists();

            if (!$viewExists) {
                $post->recordView();
            }
        }

        return $next($request);
    }
}
