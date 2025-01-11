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
        $fields = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'document_number' => 'required',
            'birthdate' => 'required',
            'phone' => 'required',
            'status' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif'
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('partners', 'public');
            $fields['photo'] = $photoPath; 
        }

        $partner = Partner::create($fields);

        return response()->json($partner, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partner $partner)
    {

        // Validación de los campos
        $fields = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'document_number' => 'required',
            'birthdate' => 'required',
            'phone' => 'required',
            'status' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif'
        ]);


        if ($request->hasFile('photo')) {

            if ($partner->photo && Storage::exists("public/{$partner->photo}")) {
                Storage::delete("public/{$partner->photo}");
            }

            $photoPath = $request->file('photo')->store('partners', 'public');
            $fields['photo'] = $photoPath;
        }

        if (empty($fields['photo'])) {
            unset($fields['photo']);
        }

        $partner->update($fields);

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
