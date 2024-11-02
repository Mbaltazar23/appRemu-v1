<?php

namespace App\Http\Controllers;

use App\Http\Requests\SustainerFormRequest;
use App\Models\Sustainer;

class SustainerController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Sustainer::class, 'sustainer');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sustainers = Sustainer::query()
            ->orderBy('id', 'ASC')
            ->paginate(5); // Puedes aplicar paginación si es necesario

        return view('sustainers.index', compact('sustainers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $sustainer = new Sustainer();

        return view('sustainers.create', compact('sustainer'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SustainerFormRequest $request)
    {
        $sustainer = Sustainer::create($request->validated());

        return redirect()->route('sustainers.index', $sustainer);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sustainer $sustainer)
    {
        return view('sustainers.show', compact('sustainer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sustainer $sustainer)
    {
        return view('sustainers.edit', compact('sustainer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SustainerFormRequest $request, Sustainer $sustainer)
    {
        $sustainer->update($request->validated());

        return redirect()->route('sustainers.show', $sustainer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sustainer $sustainer)
    {
        $sustainer->delete();

        return redirect()->route('sustainers.index');
    }
}
