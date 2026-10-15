<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Table;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user->hasPermission(Permission::DashboardView)) {
            return redirect()->route($user->homeRoute());
        }

        $todayOrders = Sale::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled');

        return view('dashboard', [
            'todaySales' => Sale::whereDate('paid_at', today())
                ->where('payment_status', 'paid')
                ->where('status', '!=', 'cancelled')
                ->sum('total'),
            'todayOrders' => $todayOrders->count(),
            'openOrders' => Sale::open()->count(),
            'occupiedTables' => Table::where('status', 'occupied')->count(),
            'totalTables' => Table::count(),
            'lowStock' => $user->hasPermission(Permission::InventoryManage)
                ? Product::lowStock()->orderBy('stock')->limit(5)->get()
                : collect(),
            'lowStockCount' => Product::lowStock()->count(),
            'recentOrders' => Sale::with(['table', 'user'])->latest()->limit(8)->get(),
        ]);
    }
}
