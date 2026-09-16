<?php

namespace App\Http\Controllers;

use App\Models\AccessActivity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessActivityController extends Controller
{
    /**
     * Display access activity history.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));
        $action = $request->input('action');

        $activities = AccessActivity::query()
            ->with('actor')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%");
                });
            })
            ->when($action, function ($query) use ($action) {
                $query->where('action', $action);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $actions = AccessActivity::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('access-activities.index', compact(
            'activities',
            'actions',
            'search',
            'action'
        ));
    }
}