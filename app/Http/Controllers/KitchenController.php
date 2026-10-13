<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public const COLUMNS = [
        'sent' => 'New',
        'preparing' => 'Preparing',
        'ready' => 'Ready to serve',
    ];

    public function index(): View
    {
        $orders = Sale::with(['table', 'items'])
            ->whereIn('status', array_keys(self::COLUMNS))
            ->orderBy('sent_at')
            ->orderBy('id')
            ->get()
            ->groupBy('status');

        return view('kitchen.index', [
            'columns' => self::COLUMNS,
            'orders' => $orders,
        ]);
    }
}
