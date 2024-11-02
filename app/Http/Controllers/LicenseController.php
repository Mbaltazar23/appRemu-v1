<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Worker;
use App\Http\Requests\LicenseFormRequest;

class LicenseController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(License::class, 'license');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Filtrar las licencias de los trabajadores pertenecientes a la escuela del usuario autenticado
        $licenses = License::whereHas('worker', function($query) {
            $query->where('school_id', auth()->user()->school_id_session);
        })
        ->orderBy('id', 'ASC')
        ->paginate(5); // Puedes aplicar paginación si es necesario

        return view('licenses.index', compact('licenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $license = new License();
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get();


        return view('licenses.create', compact('license', 'workers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LicenseFormRequest $request)
    {
        $license = License::create($request->validated());

        return redirect()->route('licenses.index')->with('success', 'Licencia creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(License $license)
    {
        return view('licenses.show',compact('license', 'workers'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(License $license)
    {
        return view('licenses.edit', compact('license'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LicenseFormRequest $request, License $license)
    {
        $license->update($request->validated());

        return redirect()->route('licenses.show', $license)->with('success', 'Licencia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(License $license)
    {
        $license->delete();

        return redirect()->route('licenses.index')->with('success', 'Licencia eliminada exitosamente.');
    }
}

