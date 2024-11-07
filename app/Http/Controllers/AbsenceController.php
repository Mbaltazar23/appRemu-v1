<?php

namespace App\Http\Controllers;

use App\Http\Requests\AbsenceFormRequest;
use App\Models\Absence;
use App\Models\Worker;

class AbsenceController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Absence::class, 'absence');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Filtrar las ausencias de los trabajadores pertenecientes a la escuela del usuario autenticado
        $absences = Absence::whereHas('worker', function ($query) {
            $query->where('school_id', auth()->user()->school_id_session);
        })
            ->orderBy('id', 'ASC')
            ->paginate(5); // Paginación si es necesario

        return view('absences.index', compact('absences'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $absence = new Absence();
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get();

        return view('absences.create', compact('absence', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AbsenceFormRequest $request)
    {
        Absence::create($request->validated());

        return redirect()->route('absences.index')->with('success', 'Ausencia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Absence $absence)
    {
        return view('absences.show', compact('absence'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Absence $absence)
    {
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get();
        return view('absences.edit', compact('absence', 'workers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AbsenceFormRequest $request, Absence $absence)
    {
        $absence->update($request->validated());

        return redirect()->route('absences.show', $absence)->with('success', 'Ausencia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absence $absence)
    {
        $absence->delete();

        return redirect()->route('absences.index')->with('success', 'Ausencia eliminada exitosamente.');
    }
}
