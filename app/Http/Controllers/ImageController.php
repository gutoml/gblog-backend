<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ImageRequest;
use App\Services\ImageStoreService;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function __construct(
        private Image $image
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $this->image->orderBy('created_at', 'desc')->paginate($request->has('per_page') ? $request->per_page : 20);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ImageRequest $request)
    {
        $responseData = [];

        foreach ($request->images as $image) {
            $imageStoreService = new ImageStoreService();
            $responseUpload = $imageStoreService->execute($image);
            if (! $image = $this->image->create($responseUpload)) {
                return response()->json(['error' => 'It was not possible to register the images in the database']);
            }

            $responseData[] = [
                'id' => $image->id,
                'name' => $image->name,
                'url' => $image->url,
            ];
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
