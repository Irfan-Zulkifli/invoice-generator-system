<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $monthlyCounts = Sale::where('user_id', auth()->user()->id)
        ->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_key'),
            DB::raw('DATE_FORMAT(created_at, "%M %Y") as month_name'),
            DB::raw('count(*) as total_count')
        )
        ->groupBy('month_key', 'month_name')
        ->orderBy('month_key', 'asc')
        ->get();

        // return product dan bilangan ikut bulan
        // name: sambal
        // data: [44, 25, 32, ...]

        $rawProductSold = Sale::where('sales.user_id', auth()->user()->id)
        ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->select(
            'products.name as product_name',
            DB::raw('DATE_FORMAT(sales.created_at, "%Y-%m") as month_key'),
            DB::raw('SUM(sale_items.quantity) as total_sold')
        )
        ->groupBy('products.id', 'products.name', 'month_key')
        ->get();

        $allMonths = $monthlyCounts->pluck('month_key')->toArray(); 

        // 3. Group the raw database results by the product name
        $groupedByProduct = $rawProductSold->groupBy('product_name');

        // 4. Transform it into the exact structure ApexCharts needs
        $productSeries = [];
        
        foreach ($groupedByProduct as $productName => $salesData) {
            $dataPoints = [];
            
            // Loop through EVERY month on the X-axis
            foreach ($allMonths as $month) {
                // Look for sales of this product in this specific month
                $saleInMonth = $salesData->firstWhere('month_key', $month);
                
                // If it exists, push the number. If they sold nothing this month, push a 0.
                $dataPoints[] = $saleInMonth ? (int) $saleInMonth->total_sold : 0;
            }
            
            $productSeries[] = [
                'name' => $productName,
                'data' => $dataPoints
            ];
        }

        $monthName = $monthlyCounts->pluck('month_name');
        $monthCount = $monthlyCounts->pluck('total_count');

        // $countSalesByMonth = $saleThisYear->groupBy()

        return view('pages.dashboard', compact(
            'title', 
            'breadcrumbs', 
            'totalCustomers', 
            'totalProducts', 
            'totalSales', 
            'totalRevenue', 
            'recentSales',
            'monthName',
            'monthCount',
            'productSeries'
        ));
    }
}
