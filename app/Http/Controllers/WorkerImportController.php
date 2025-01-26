<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExcelImportFormRequest;
use App\Imports\WorkersImport;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class WorkerImportController extends Controller
{
    
    public function __invoke(ExcelImportFormRequest $request)
    {
        $user = auth()->user();
        $file = $request->file('file');

        Excel::import(new WorkersImport($user->school_id_session), $file);

        $errors = Session::get('import_errors');

        if (!empty($errors)) {
            return redirect()->back()->withErrors($errors);
        }

        return back()->with('success', 'La importación se ha completado con éxito.');
    }
}
