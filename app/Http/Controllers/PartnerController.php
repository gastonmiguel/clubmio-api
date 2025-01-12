<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Services\ImageService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function show(int $id)
    {
        $partner = Partner::find($id);
        return $partner;
    }

    public function index(Request $request)
    {
        $query = $request->query('query', '');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 6);

        $partnersQuery = Partner::query();

        if (!empty($query)) {
            $partnersQuery->where(function ($subQuery) use ($query) {
                $subQuery->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('surname', 'LIKE', "%{$query}%")
                    ->orWhere('birthdate', 'LIKE', "%{$query}%")
                    ->orWhere('document_number', 'LIKE', "%{$query}%");
            });
        }

        $partners = $partnersQuery
            ->skip($offset)
            ->take($limit)
            ->get();

        return $partners;
    }

    public function getTotalPages(Request $request)
    {

        $query = $request->query('query', '');
        $itemsPerPage = $request->query('itemsPerPage', 6);

        $totalRecords = Partner::where('name', 'LIKE', "%{$query}%")
            ->orWhere('surname', 'LIKE', "%{$query}%")
            ->count();

        $totalPages = ceil($totalRecords / $itemsPerPage);

        return ['totalPages' => $totalPages];
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'document_number' => 'required|string',
            'birthdate' => 'required|date',
            'phone' => 'required|string',
            'photo' => 'nullable|string', // Base64.
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->has('photo') && $request->photo) {
            $imagePath = $this->imageService->saveImage($request->photo);

            if ($imagePath === false) {
                return response()->json([
                    'message' => 'Unsupported image format or error saving the image.',
                ], 400);
            }

            $validatedData['photo'] = $imagePath;
        } else {
            $validatedData['photo'] = 'partners/default_avatar.png';
        }

        $partner = Partner::create($validatedData);

        return response()->json($partner, 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'document_number' => 'required|string',
            'birthdate' => 'required|date',
            'phone' => 'required|string',
            'photo' => 'nullable|string', // Base64.
            'status' => 'required|in:active,inactive',
        ]);

        $partner = Partner::find($id);

        if ($request->has('photo') && $request->photo) {
            $oldImagePath = $partner->photo;

            $imagePath = $this->imageService->saveImage($request->photo);

            if ($imagePath === false) {
                return response()->json([
                    'message' => 'Unsupported image format or error saving the image.',
                ], 400);
            }

            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                $this->imageService->deleteImage($oldImagePath);
            }

            $validatedData['photo'] = $imagePath;
        } else {
            unset($validatedData['photo']);
        }

        $partner->update($validatedData);

        return response()->json($partner, 200);
    }

    public function destroy($id)
    {
        // $partner = Partner::find($id);
        // $partner->delete()
    }
}
