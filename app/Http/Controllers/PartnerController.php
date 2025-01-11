<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{

    /**
     * Display the specified resource.
     */
    public function show(Partner $partner)
    {
        return $partner;
    }

    /**
     * Display a listing of the resource.
     */
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los campos
        $validatedData = $request->validate([
            'name' => 'required|string',
            'surname' => 'required|string',
            'document_number' => 'required|string',
            'birthdate' => 'required|date',
            'phone' => 'required|string',
            'photo' => 'nullable|string', // Base64
            'status' => 'required|in:active,inactive',
        ]);
    
        if (!empty($validatedData['photo'])) {
            $imageData = base64_decode($validatedData['photo']);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
            ];
    
            if (!isset($extensions[$mimeType])) {
                return response()->json([
                    'message' => 'Unsupported image format.',
                ], 400);
            }

            $extension = $extensions[$mimeType];
            $imageName = uniqid() . '.' . $extension;

            $photoPath = 'partners/' . $imageName;
            Storage::put($photoPath, $imageData);
            $validatedData['photo'] = $photoPath;
        } else {
            $validatedData['photo'] = 'partners/default_avatar.png';
        }

        $partner = Partner::create($validatedData);

        return response()->json($partner, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partner $partner)
    {
        // Validación de los campos
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
            $oldImagePath = $partner->photo;
            $imageData = base64_decode($request->photo);
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
            ];
    
            if (!isset($extensions[$mimeType])) {
                return response()->json([
                    'message' => 'Unsupported image format.',
                ], 400);
            }
    
            $extension = $extensions[$mimeType];
            $imageName = uniqid() . '.' . $extension;
            $imagePath = 'partners/' . $imageName;
    
            Storage::disk('public')->put($imagePath, $imageData);
    
            if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
                Storage::disk('public')->delete($oldImagePath);
            }

            $validatedData['photo'] = $imagePath;
        } else {
            unset($validatedData['photo']);
        }

        $partner->update($validatedData);

        return response()->json($partner, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        // $partner->delete()
    }
}
