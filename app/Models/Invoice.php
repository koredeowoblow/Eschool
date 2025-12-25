<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasTenancyTrait;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, HasTenancyTrait;

    const STATUS_PENDING = 'pending';
    const STATUS_PARTIAL = 'partial';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'student_id',
        'session_id',
        'term_id',
        'invoice_number',
        'total_amount',
        'status',
        'due_date',
        'school_id',
        'notes',
        'discount_amount',
        'discount_percentage',
    ];

    protected $appends = [
        'paid_amount',
        'balance',
        'is_overdue',
        'payment_status',
    ];

    protected $casts = [
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
    ];

    /**
     * Get the invoice items.
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the payments for this invoice.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the student that owns the invoice.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the term associated with the invoice.
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the session associated with the invoice.
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the school that owns the invoice.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the total amount paid for this invoice.
     */
    public function getPaidAmountAttribute()
    {
        return $this->payments->sum('amount');
    }

    /**
     * Get the remaining balance for this invoice.
     */
    public function getBalanceAttribute()
    {
        $totalAfterDiscount = $this->total_amount;

        if ($this->discount_amount > 0) {
            $totalAfterDiscount -= $this->discount_amount;
        } elseif ($this->discount_percentage > 0) {
            $totalAfterDiscount -= ($this->total_amount * $this->discount_percentage / 100);
        }

        return max(0, $totalAfterDiscount - $this->paid_amount);
    }

    /**
     * Check if the invoice is overdue.
     */
    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && $this->balance > 0;
    }

    /**
     * Get the payment status based on payments and due date.
     */
    public function getPaymentStatusAttribute()
    {
        if ($this->status === self::STATUS_CANCELLED) {
            return self::STATUS_CANCELLED;
        }

        if ($this->balance <= 0) {
            return self::STATUS_PAID;
        }

        if ($this->is_overdue) {
            return self::STATUS_OVERDUE;
        }

        if ($this->paid_amount > 0) {
            return self::STATUS_PARTIAL;
        }

        return self::STATUS_PENDING;
    }

    /**
     * Update the invoice status based on payments.
     */
    public function updatePaymentStatus()
    {
        $this->status = $this->payment_status;
        $this->save();

        return $this;
    }

    /**
     * Scope a query to only include overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_PARTIAL]);
    }

    /**
     * Scope a query to only include pending invoices.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
}
