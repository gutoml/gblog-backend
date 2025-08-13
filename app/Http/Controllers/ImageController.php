<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ImageRequest;
use App\Services\ImageStoreService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class ImageController extends Controller
{
    public function __construct(
        private Image $image
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): LengthAwarePaginator
    {
        return $this->image
            ->latest()
            ->paginate($request->has('per_page') ? $request->per_page : 20);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ImageRequest $request, ImageStoreService $imageStoreService): JsonResponse
    {
        $responseData = [];

        try {
            foreach ($request->images as $image) {
                $responseUpload = $imageStoreService->execute($image);
                $responseData[] = $responseUpload;
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Image upload failed',
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json($responseData, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Image $image): JsonResponse
    {
        return response()->json($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image): JsonResponse
    {
        Storage::delete($image->url);
        $image->delete();

        return response()->json(null, 204);
    }
}
