<?php

namespace App\Http\Controllers;

use App\Models\AccessActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessActivityController extends Controller
{
    /**
     * Display access activity history.
     */
    public function index(
        Request $request
    ): View {
        $search = trim(
            $request->input('search', '')
        );

        $action = $request->input('action');

        $actorId = $request->input('actor_id');

        $dateFrom = $request->input('date_from');

        $dateTo = $request->input('date_to');

        $activities = AccessActivity::query()

            ->with('actor')

            /*
             * Search description/action.
             */
            ->when(
                $search,
                function ($query) use ($search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'description',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'action',
                            'like',
                            "%{$search}%"
                        );

                    });

                }
            )

            /*
             * Action filter.
             */
            ->when(
                $action,
                function ($query) use ($action) {

                    $query->where(
                        'action',
                        $action
                    );

                }
            )

            /*
             * Actor filter.
             */
            ->when(
                $actorId,
                function ($query) use ($actorId) {

                    $query->where(
                        'actor_id',
                        $actorId
                    );

                }
            )

            /*
             * Date from.
             */
            ->when(
                $dateFrom,
                function ($query) use ($dateFrom) {

                    $query->whereDate(
                        'created_at',
                        '>=',
                        $dateFrom
                    );

                }
            )

            /*
             * Date to.
             */
            ->when(
                $dateTo,
                function ($query) use ($dateTo) {

                    $query->whereDate(
                        'created_at',
                        '<=',
                        $dateTo
                    );

                }
            )

            ->latest()

            ->paginate(15)

            ->withQueryString();


        /*
         * Available actions.
         */
        $actions = AccessActivity::query()

            ->select('action')

            ->distinct()

            ->orderBy('action')

            ->pluck('action');


        /*
         * Available actors.
         */
        $actors = User::query()

            ->whereIn(
                'id',
                AccessActivity::query()
                    ->whereNotNull('actor_id')
                    ->select('actor_id')
                    ->distinct()
            )

            ->orderBy('name')

            ->get();


        return view(
            'access-activities.index',
            compact(
                'activities',
                'actions',
                'actors',
                'search',
                'action',
                'actorId',
                'dateFrom',
                'dateTo'
            )
        );
    }
}