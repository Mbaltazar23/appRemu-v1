<?php

namespace App\Http\Controllers;

use App\Models\Payroll;

class PayrollController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Payroll::class, 'payroll');
    }

    public function index()
    {
        // Obtener el school_id desde la sesión del usuario autenticado
        $school_id = auth()->user()->school_id_session;
        // Obtener las planillas de remuneraciones para este school_id
        $payrolls = Payroll::where('school_id', $school_id)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(5); // Puedes cambiar el 10 por el número de elementos por página que desees
        // Devolver la vista con las planillas
        return view('payrolls.index', compact('payrolls'));
    }

    public function show(Payroll $payroll)
    {
        return view('payrolls.show', compact('payroll'));
    }

    public function store()
    {
        $school_id = auth()->user()->school_id_session;

        $payroll = new Payroll();
        $payroll->generatePayroll($school_id, now()->month, now()->year);

        return redirect()->route('payrolls.index')->with('success', 'La planilla se ha registrado correctamente.');
    }
}
