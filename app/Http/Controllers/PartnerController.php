<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
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
        $fields = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'document_number' => 'required',
            'birthdate' => 'required',
            'phone' => 'required',
            'photo' => 'required',
            'status' => 'required'
        ]);

        $partner = Partner::create($fields);

        return $partner;
    }

    /**
     * Display the specified resource.
     */
    public function show(Partner $partner)
    {
        return $partner;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Partner $partner)
    {
        $fields = $request->validate([
            'name' => 'required',
            'surname' => 'required',
            'document_number' => 'required',
            'birthdate' => 'required',
            'phone' => 'required',
            'photo' => 'required',
            'status' => 'required'
        ]);

        $partner->update($fields);

        return $partner;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partner $partner)
    {
        // $partner->delete()
    }
}
