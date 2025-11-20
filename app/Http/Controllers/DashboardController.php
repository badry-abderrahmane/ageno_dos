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
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // --- Date setup for comparison periods ---

        // Last Month (for monthly revenue and new clients comparison)
        $lastMonth = $now->copy()->subMonth();
        $prevMonth = $lastMonth->month;
        $prevMonthYear = $lastMonth->year;

        // Last Year (for yearly revenue comparison)
        $prevYear = $now->copy()->subYear()->year;

        // --- Database Driver Detection for Agnosticism (Unchanged) ---
        $dbDriver = DB::connection()->getDriverName();

        // Define the function for extracting Year and Month (e.g., 'YYYY-MM')
        // SQLite uses STRFTIME, MySQL uses DATE_FORMAT
        $dateFormatFunction = match ($dbDriver) {
            'sqlite' => "STRFTIME('%Y-%m', created_at)",
            'mysql', 'mariadb' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "TO_CHAR(created_at, 'YYYY-MM')", // PostgreSQL format
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        // --- 1. Current Period Data Fetching ---

        // Current Month Revenue
        $currentRevenueMonthRaw = Invoice::where('status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('total');

        // Current Year Revenue
        $currentRevenueYearRaw = Invoice::where('status', 'paid')
            ->whereYear('created_at', $currentYear)
            ->sum('total');

        // Current Month Invoices (New Metric)
        $currentInvoicesMonth = Invoice::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();
        // ...
        // Last Month Invoices (New Metric Comparison)
        $previousInvoicesMonth = Invoice::whereYear('created_at', $prevMonthYear)
            ->whereMonth('created_at', $prevMonth)
            ->count();

        // Current Month Clients
        $currentClientsMonth = Client::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        // --- 2. Previous Period Data Fetching (for comparisons) ---

        // Last Month Revenue (vs. current month)
        $previousRevenueMonthRaw = Invoice::where('status', 'paid')
            ->whereYear('created_at', $prevMonthYear)
            ->whereMonth('created_at', $prevMonth)
            ->sum('total');

        // Last Year Revenue (vs. current year)
        $previousRevenueYearRaw = Invoice::where('status', 'paid')
            ->whereYear('created_at', $prevYear)
            ->sum('total');

        // Last Month Clients (vs. current month)
        $previousClientsMonth = Client::whereYear('created_at', $prevMonthYear)
            ->whereMonth('created_at', $prevMonth)
            ->count();


        // --- 3. Key Statistics (Stat Cards) with Comparison ---
        $stats = [
            'totalInvoices' => [
                'value' => Invoice::count(),
                'comparison' => '', // No comparison needed for overall total
            ],
            'invoicesThisMonth' => [ // <-- New Metric added here
                'value' => $currentInvoicesMonth,
                'comparison' => $this->calculatePercentageChange($currentInvoicesMonth, $previousInvoicesMonth, 'month'),
            ],
            'totalRevenue' => [
                'value' => number_format(Invoice::where('status', 'paid')->sum('total'), 2),
                'comparison' => '', // No comparison needed for overall total
            ],
            'totalRevenueMonth' => [
                'value' => number_format($currentRevenueMonthRaw, 2),
                'comparison' => $this->calculatePercentageChange($currentRevenueMonthRaw, $previousRevenueMonthRaw, 'month'),
            ],
            'totalRevenueYear' => [
                'value' => number_format($currentRevenueYearRaw, 2),
                'comparison' => $this->calculatePercentageChange($currentRevenueYearRaw, $previousRevenueYearRaw, 'year'),
            ],
            'totalClients' => [
                'value' => Client::count(),
                'comparison' => '', // No comparison needed for overall total
            ],
            'clientsThisMonth' => [
                'value' => $currentClientsMonth,
                'comparison' => $this->calculatePercentageChange($currentClientsMonth, $previousClientsMonth, 'month'),
            ],
        ];


        // --- 4. Monthly Revenue Chart Data (Line Chart - Unchanged) ---
        $sixMonthsAgo = $now->copy()->subMonths(6)->startOfMonth();

        $monthlyRevenue = Invoice::select(
            DB::raw("{$dateFormatFunction} as month_year"),
            DB::raw('SUM(total) as revenue') // Assuming total is stored as a float/decimal type
        )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->where('status', 'paid')
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

        // --- 5. Clients by Sector Chart Data (Pie Chart - Unchanged) ---
        $sectors = Client::select('sector', DB::raw('count(*) as count'))
            ->whereNotNull('sector')
            ->groupBy('sector')
            ->orderBy('count', 'desc')
            ->get();

        $sectorChart = [
            'series' => $sectors->pluck('count')->toArray(),
            'labels' => $sectors->pluck('sector')->toArray(),
        ];


        // --- 6. Render Inertia View ---
        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'monthlyRevenueData' => $revenueChart,
            'clientsBySectorData' => $sectorChart,
        ]);
    }

    /**
     * Calculates the percentage change and formats the output string.
     *
     * @param float|int $current The current period's value.
     * @param float|int $previous The previous period's value.
     * @param string $period 'month' or 'year' for comparison text.
     * @return string E.g., "+20% from last month"
     */
    private function calculatePercentageChange($current, $previous, string $period): string
    {
        $comparisonText = $period === 'month' ? 'mois dernier' : 'année dernière';
        $current = (float)$current;
        $previous = (float)$previous;

        if ($previous == 0) {
            if ($current > 0) {
                // If previous was 0 and current > 0, it's 100% growth or new data.
                return '+100% vs ' . $comparisonText;
            }
            // If both are 0, no change.
            return '0% Aucun changement ' . $comparisonText;
        }

        $change = (($current - $previous) / $previous) * 100;

        // Determine sign and direction
        $sign = $change >= 0 ? '+' : '';
        $direction = $change >= 0 ? 'vs' : 'vs'; // Using 'from' for consistency

        return sprintf('%s%.0f%% %s %s', $sign, $change, $direction, $comparisonText);
    }
}
