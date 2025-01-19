<?php
namespace App\Http\Controllers;

use App\Models\Payroll;

class PayrollController extends Controller
{
    /**
     * Create the controller instance.
     *
     * This constructor authorizes resource actions for the Payroll model.
     * Ensures that only authorized users can access payroll-related actions.
     */
    public function __construct()
    {
        $this->authorizeResource(Payroll::class, 'payroll');
    }
    /**
     * Display a listing of the payrolls.
     *
     * This method retrieves the payrolls for the currently authenticated user's school.
     * It orders them by year and month in descending order, and paginates the results.
     */
    public function index()
    {
        // Get the school_id from the authenticated user's session
        $school_id = auth()->user()->school_id_session;
        // Get the payrolls for this school_id
        $payrolls = Payroll::where('school_id', $school_id)
            ->orderBy('year', 'desc')  // Order by year in descending order
            ->orderBy('month', 'desc') // Order by month in descending order
            ->paginate(5); // You can change the 5 to the number of items per page you want

        // Return the view with the payrolls
        return view('payrolls.index', compact('payrolls'));
    }
    /**
     * Display the specified payroll.
     *
     * This method shows the details of a specific payroll.
     */
    public function show(Payroll $payroll)
    {
        return view('payrolls.show', compact('payroll'));
    }
    /**
     * Store a newly created payroll.
     *
     * This method generates a new payroll for the authenticated user's school.
     * The payroll is created for the current month and year.
     */
    public function store()
    {
        // Get the school_id from the authenticated user's session
        $school_id = auth()->user()->school_id_session;
        // We instantiate the object for the spreadsheet to save
        $payroll = new Payroll();
        // Generate the payroll for the current month and year
        $payroll->generatePayroll($school_id, now()->month, now()->year);
        // Redirect to the payrolls index page with a success message
        return redirect()->route('payrolls.index')->with('success', 'La planilla se ha registrado correctamente.');
    }
}
