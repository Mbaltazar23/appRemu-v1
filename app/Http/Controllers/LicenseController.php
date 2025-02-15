<?php

namespace App\Http\Controllers;

use App\Http\Requests\LicenseFormRequest;
use App\Models\License;
use App\Models\Worker;

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
        // Filter the licenses of workers belonging to the authenticated user's school
        $licenses = License::getLicensesBySchool(auth()->user()->school_id_session)->paginate(5);

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
        // Create the license
        $license = License::create($request->validated());
        // Get license date
        $arr = explode("-", $request->input('issue_date')); // Date in format dd-mm/yyyy
        $day = (int) $arr[2];
        $month = (int) $arr[1];
        $year = (int) $arr[0];
        // Get worker associated with the license
        $worker = $license->worker;
        // If the worker is a teacher, we update the hours of the license
        if ($worker->worker_type == Worker::WORKER_TYPE_TEACHER) {
            $license->updateLicenseHours($day, $month, $year, $request->input('days'));
        }
        // If the worker is not a teacher, we only update the days
        if ($worker->worker_type == Worker::WORKER_TYPE_NON_TEACHER) {
            $license->updateLicenseDays($day, $month, $year, $request->input('days'));
        }
        return redirect()->route('licenses.index')->with('success', 'Licencia creada exitosamente.');
    }
    /**
     * Display the specified resource.
     */
    public function show(License $license)
    {
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get();

        return view('licenses.show', compact('license', 'workers'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(License $license)
    {
        $workers = Worker::where('school_id', auth()->user()->school_id_session)->get();

        return view('licenses.edit', compact('license', 'workers'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(LicenseFormRequest $request, License $license)
    {
        // Update the license
        $license->update($request->validated());
        // Get license date
        $arr = explode("-", $request->input('issue_date')); // Date in format dd-mm/yyyy
        $day = (int) $arr[2];
        $month = (int) $arr[1];
        $year = (int) $arr[0];
        // Get worker associated with the license
        $worker = $license->worker;
        // If the worker is a teacher, we update the hours of the license
        if ($worker->worker_type == Worker::WORKER_TYPE_TEACHER) {
            $license->updateLicenseHours($day, $month, $year, $request->input('days'));
        }
        // If the worker is not a teacher, we only update the days
        if ($worker->worker_type == Worker::WORKER_TYPE_NON_TEACHER) {
            $license->updateLicenseDays($day, $month, $year, $request->input('days'));
        }

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
