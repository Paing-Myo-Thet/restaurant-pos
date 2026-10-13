<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Table;

class DashboardController extends Controller
{
    public function index(): View
    {
    $todaySales = Sale::whereDate('created_at', today())
    ->where('payment_status', 'paid')
    ->sum('total');
    $todayOrders = Sale::count();
    $occupiedTables = Table::where('status', 'occupied')->count();
    

    return view('dashboard', compact('todaySales','todayOrders','occupiedTables'));

    
    }
    
}
