<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $customerQuery = Customer::where('seller_id', $user->id);
        $productQuery = Product::where('creator_id', $user->id);
        $saleQuery = Sale::where('user_id', $user->id);
        
        $paymentQuery = Payment::whereHas('sale', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

        if ($startDate) {
            $customerQuery->whereDate('created_at', '>=', $startDate);
            $productQuery->whereDate('created_at', '>=', $startDate);
            $saleQuery->whereDate('created_at', '>=', $startDate);
            $paymentQuery->whereDate('payment_date', '>=', $startDate);
        }

        if ($endDate) {
            $customerQuery->whereDate('created_at', '<=', $endDate);
            $productQuery->whereDate('created_at', '<=', $endDate);
            $saleQuery->whereDate('created_at', '<=', $endDate);
            $paymentQuery->whereDate('payment_date', '<=', $endDate);
        }

        $totalCustomers = $customerQuery->count();
        $totalProducts = $productQuery->count();
        $totalSales = $saleQuery->count();
        $totalRevenue = $paymentQuery->sum('amount');

        // Recent 5 sales (always based on creation date)
        $recentSales = Sale::with(['buyer'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $title = 'Dashboard';
        $breadcrumbs = [
            'Dashboard' => false,
        ];

        return view('pages.dashboard', compact(
            'title', 
            'breadcrumbs', 
            'totalCustomers', 
            'totalProducts', 
            'totalSales', 
            'totalRevenue', 
            'recentSales'
        ));
    }
}
