<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenancyTrait;

class Payment extends Model
{
    use HasFactory, HasTenancyTrait;

    const METHOD_CASH = 'cash';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_CHEQUE = 'cheque';
    const METHOD_ONLINE = 'online';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'invoice_id',
        'student_id',
        'amount',
        'method',
        'payment_date',
        'transaction_ref',
        'school_id',
        'status',
        'notes',
        'receipt_number',
        'payment_proof',
        'processed_by',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the invoice that owns the payment.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the student that made the payment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }



    /**
     * Get the user who processed the payment.
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include completed payments.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include payments for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Update the related invoice status after payment is saved.
     */
    protected static function booted()
    {
        static::saved(function ($payment) {
            if ($payment->invoice && $payment->status === self::STATUS_COMPLETED) {
                $payment->invoice->updatePaymentStatus();
            }
        });
    }
}
