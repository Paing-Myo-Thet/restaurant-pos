<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public const PAYMENT_METHODS = ['cash', 'card', 'mobile'];

    /**
     * Allowed status changes and the roles that may make each one.
     */
    public const TRANSITIONS = [
        'pending' => [
            'sent' => ['admin', 'cashier', 'waiter'],
            'cancelled' => ['admin', 'cashier', 'waiter'],
        ],
        'sent' => [
            'preparing' => ['admin', 'kitchen'],
            'cancelled' => ['admin', 'cashier'],
        ],
        'preparing' => [
            'ready' => ['admin', 'kitchen'],
            'cancelled' => ['admin'],
        ],
        'ready' => [
            'served' => ['admin', 'cashier', 'waiter'],
        ],
    ];

    protected $fillable = [
        'customer_id',
        'table_id',
        'user_id',
        'sub_total',
        'descount',
        'total',
        'status',
        'payment_status',
        'note',
        'payment_method',
        'amount_received',
        'change_due',
        'sent_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'sub_total' => 'decimal:2',
            'descount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_received' => 'decimal:2',
            'change_due' => 'decimal:2',
            'sent_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getOrderNumberAttribute(): string
    {
        return '#'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Orders still in progress: not cancelled, and not both served and paid.
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', 'cancelled')
            ->where(fn (Builder $q) => $q
                ->where('status', '!=', 'served')
                ->orWhere('payment_status', 'unpaid'));
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function isEditable(): bool
    {
        return ! $this->isPaid() && in_array($this->status, ['pending', 'sent'], true);
    }

    public function canCheckout(): bool
    {
        return ! $this->isPaid() && $this->status !== 'cancelled';
    }

    /**
     * Waiters print the (draft) bill once the food is served; cashiers and
     * admins can print or reprint at any time.
     */
    public function canBePrintedBy(User $user): bool
    {
        if ($user->hasRole('waiter')) {
            return $this->status === 'served';
        }

        return $user->hasRole('admin', 'cashier');
    }

    /**
     * Status changes the given user may make from the current status.
     *
     * @return list<string>
     */
    public function nextStatusesFor(User $user): array
    {
        $next = [];

        foreach (self::TRANSITIONS[$this->status] ?? [] as $status => $roles) {
            if ($status === 'cancelled' && $this->isPaid()) {
                continue;
            }

            if ($user->hasRole(...$roles)) {
                $next[] = $status;
            }
        }

        return $next;
    }
}
