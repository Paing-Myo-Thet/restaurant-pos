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

    /**
     * How long a cancelled order stays on the kitchen screen as a STOP notice.
     */
    public const CANCELLED_NOTICE_MINUTES = 30;

    public function index(): View
    {
        $orders = Sale::with(['table', 'items'])
            ->whereIn('status', array_keys(self::COLUMNS))
            ->orderBy('sent_at')
            ->orderBy('id')
            ->get()
            ->groupBy('status');

        // Orders the kitchen had received but that were then cancelled, so cooks stop making them.
        $cancelled = Sale::with(['table', 'items'])
            ->where('status', 'cancelled')
            ->whereNotNull('sent_at')
            ->where('cancelled_at', '>=', now()->subMinutes(self::CANCELLED_NOTICE_MINUTES))
            ->latest('cancelled_at')
            ->get();

        return view('kitchen.index', [
            'columns' => self::COLUMNS,
            'orders' => $orders,
            'cancelled' => $cancelled,
        ]);
    }
}
