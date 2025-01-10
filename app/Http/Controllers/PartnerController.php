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
    public function index()
    {
        return Partner::all();
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
