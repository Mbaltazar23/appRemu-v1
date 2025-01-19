<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Support\Facades\Auth;


class HistoryController extends Controller
{
    public function __construct()
    {
        // We use authorizeResource to authorize access to the resource
        $this->authorizeResource(History::class, 'history');
    }
    public function index()
    {
        // Get records with pagination
        $historys = History::query()
            ->orderBy('user_id', 'ASC')
            ->paginate(5);

        // Check if the user has permission to view history
        if (Auth::check()) {
            return view('historys.index', compact('historys'));
        } else {
            abort(403, 'No autorizado');
        }
    }
}
