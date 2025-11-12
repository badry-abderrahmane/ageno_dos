<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Prepare data for the dashboard and render the Inertia page.
     */
    public function index()
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;
        // --- 1. Database Driver Detection for Agnosticism ---
        $dbDriver = DB::connection()->getDriverName();

        // Define the function for extracting Year and Month (e.g., 'YYYY-MM')
        // SQLite uses STRFTIME, MySQL uses DATE_FORMAT
        $dateFormatFunction = match ($dbDriver) {
            'sqlite' => "STRFTIME('%Y-%m', created_at)",
            'mysql', 'mariadb' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')", // PostgreSQL format
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        // --- 2. Key Statistics (Stat Cards) ---
        $stats = [
            'totalInvoices' => Invoice::count(),
            // Only count "paid" invoices for total revenue
            'totalRevenue' => number_format(Invoice::where('status', 'paid')->sum('total'), 2),
            'totalRevenueMonth' => number_format(
                Invoice::where('status', 'paid')
                    ->whereYear('created_at', $currentYear) // Ensures accuracy across year boundaries
                    ->whereMonth('created_at', $currentMonth)
                    ->sum('total'),
                2
            ),
            'totalRevenueYear' => number_format(
                Invoice::where('status', 'paid')
                    ->whereYear('created_at', $currentYear) // Ensures accuracy across year boundaries
                    ->sum('total'),
                2
            ),
            'totalClients' => Client::count(),
            'clientsThisMonth' => Client::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // --- 3. Monthly Revenue Chart Data (Line Chart) ---
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

        $monthlyRevenue = Invoice::select(
            DB::raw("{$dateFormatFunction} as month_year"),
            DB::raw('SUM(total) as revenue')
        )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->get();

        $revenueChart = [
            'categories' => $monthlyRevenue->pluck('month_year')->toArray(),
            'series' => [[
                'name' => 'Revenue',
                'data' => $monthlyRevenue->pluck('revenue')->map(fn($rev) => (float)$rev)->toArray()
            ]]
        ];

        // --- 4. Clients by Sector Chart Data (Pie Chart) ---
        $sectors = Client::select('sector', DB::raw('count(*) as count'))
            ->whereNotNull('sector')
            ->groupBy('sector')
            ->orderBy('count', 'desc')
            ->get();

        $sectorChart = [
            'series' => $sectors->pluck('count')->toArray(),
            'labels' => $sectors->pluck('sector')->toArray(),
        ];


        // --- 5. Render Inertia View ---
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'monthlyRevenueData' => $revenueChart,
            'clientsBySectorData' => $sectorChart,
        ]);
    }
}
