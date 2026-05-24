<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard (Request $request)
    {
        $user = auth()->user();
        $salesNum = Sale::where('user_id', $user->id)->count();
        $title = 'Dashboard';
        $breadcrumbs = [
            'Dashboard' => false,
        ];

        return view('pages.dashboard', compact('title', 'breadcrumbs'));
    }
}
