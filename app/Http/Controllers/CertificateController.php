<?php
namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Worker;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $school_id = auth()->user()->school_id_session;

        $certificates = Certificate::getCertificateForSchool($school_id);

        return view('certificates.index', compact('certificates'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $year = $request->input('year');

        $school_id = auth()->user()->school_id_session;

        $workers = Worker::where('school_id', $school_id)->get();

        foreach ($workers as $worker) {
            Certificate::createCertificate($school_id, $worker->id, $year);
        }

        return redirect()->route('certificates.index')->with('success', 'Los certificados se han creado con exito');
    }
    /**
     * Print the specified resource.
     */
    public function printer($year)
    {
        $school_id          = auth()->user()->school_id_session;
        $impresCertificates = Certificate::getCertificates($year, $school_id);
        return view('certificates.show', ['workersData' => $impresCertificates]);
    }
    /**
     * View the specified resource.
     */
    public function view($year)
    {
        $school_id = auth()->user()->school_id_session;
        $impresCertificates = Certificate::getCertificates($year, $school_id);
        return view('certificates.view', ['workersData' => $impresCertificates]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($year)
    {
        $school_id = auth()->user()->school_id_session;

        // Delete certificates for the specified year
        Certificate::where('school_id', $school_id)
            ->where('year', $year)
            ->delete();

        return redirect()->route('certificates.index')->with('success', 'Certificados eliminados exitosamente.');
    }

}
