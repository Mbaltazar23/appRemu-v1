<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolFormRequest;
use App\Models\School;
use App\Models\Sustainer;

class SchoolController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(School::class, 'school');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $schools = School::query()
            ->orderBy('id', 'DESC')
            ->paginate(5);

        return view('schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $school = new School();
        $sustainers = Sustainer::all();

        // Cargar las opciones directamente desde el controlador
        $dependencyOptions = School::DEPENDENCY_OPTIONS;
        $granttOptions = School::GRANTT_OPTIONS;

        return view('schools.create', compact('school', 'sustainers', 'dependencyOptions', 'granttOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SchoolFormRequest $request)
    {
        $school = School::create($request->all());

        return redirect()->route('schools.index', $school);
    }

    /**
     * Display the specified resource.
     */
    public function show(School $school)
    {
        return view('schools.show', compact('school'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(School $school)
    {
        $sustainers = Sustainer::all();

        // Cargar las opciones directamente desde el controlador
        $dependencyOptions = School::DEPENDENCY_OPTIONS;
        $granttOptions = School::GRANTT_OPTIONS;

        return view('schools.edit', compact('school', 'sustainers', 'dependencyOptions', 'granttOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SchoolFormRequest $request, School $school)
    {
        $school->update($request->all());

        return redirect()->route('schools.index', $school);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(School $school)
    {
        $school->delete();

        return redirect()->route('schools.index');
    }
}
