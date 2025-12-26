<?php

namespace App\Repositories\Fees;

use App\Models\Payment;
use App\Repositories\BaseRepository;

class PaymentRepository extends BaseRepository
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    /**
     * Scoped query: Enforce student ownership for student users.
     */
    public function query()
    {
        $query = parent::query();
        /** @var \App\Models\User $user */
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->hasRole('Student')) {
            $studentId = $user->student()->value('id');
            if ($studentId) {
                $query->where('student_id', $studentId);
            } else {
                $query->where('id', 0); // Safe failure for orphaned users
            }
        } elseif ($user && $user->hasRole('Guardian')) {
            // Security: Enforce child-scoping for parents
            $studentIds = $user->guardianStudents()->pluck('id');
            if ($studentIds->isNotEmpty()) {
                $query->whereIn('student_id', $studentIds);
            } else {
                $query->where('id', 0); // Safe failure for orphaned guardians
            }
        }

        return $query;
    }

    /**
     * List payments with filters.
     */
    public function list(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->query(); // Automatically scoped

        if (!empty($filters['student_id'])) {
            $query->where('student_id', $filters['student_id']);
        }

        if (!empty($filters['invoice_id'])) {
            $query->where('invoice_id', $filters['invoice_id']);
        }

        if (!empty($filters['method'])) {
            $query->where('method', $filters['method']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

        $perPage = $filters['per_page'] ?? 15;

        return $query->with(['invoice', 'student', 'processedBy'])
            ->latest('payment_date')
            ->paginate($perPage);
    }
}
