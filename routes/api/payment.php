<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\FeeTypeController;
use App\Http\Controllers\Payment\InvoiceController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\InvoiceItemController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Invoices and Payments
    Route::get('invoices', [InvoiceController::class, 'index']);
    Route::get('invoices/{id}', [InvoiceController::class, 'show']);
    Route::get('student/{studentId}/invoices', [InvoiceController::class, 'getStudentInvoices']);

    // Mutating Invoice Actions - Protected by Session Lock
    Route::middleware(['check.session'])->group(function () {
        Route::post('invoices', [InvoiceController::class, 'store']);
        Route::put('invoices/{id}', [InvoiceController::class, 'update']);
        Route::delete('invoices/{id}', [InvoiceController::class, 'destroy']);
        Route::post('invoices/bulk-generate', [InvoiceController::class, 'bulkGenerate']);
        Route::post('invoices/{id}/mark-as-paid', [InvoiceController::class, 'markAsPaid']);
    });

    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('payments/{id}', [PaymentController::class, 'show']);
    Route::get('student/{studentId}/payments', [PaymentController::class, 'getStudentPayments']);
    Route::get('invoice/{invoiceId}/payments', [PaymentController::class, 'getInvoicePayments']);
    Route::get('payments/{id}/receipt', [PaymentController::class, 'generateReceipt']);

    // Mutating Payment Actions - Protected by Session Lock
    Route::middleware(['check.session'])->group(function () {
        Route::post('payments', [PaymentController::class, 'store']);
        Route::put('payments/{id}', [PaymentController::class, 'update']);
        Route::delete('payments/{id}', [PaymentController::class, 'destroy']);
        Route::post('payments/{id}/verify', [PaymentController::class, 'verifyPayment']);
    });

    Route::get('fee-types', [FeeTypeController::class, 'index']);
    Route::get('fee-types/{id}', [FeeTypeController::class, 'show']);
    Route::get('fee-types/{id}/usage-stats', [FeeTypeController::class, 'getUsageStats']);
    Route::get('fee-types-summary', [FeeTypeController::class, 'getFeeTypesSummary']);

    // Mutating Fee Type Actions - Protected by Session Lock
    Route::middleware(['check.session'])->group(function () {
        Route::post('fee-types', [FeeTypeController::class, 'store']);
        Route::put('fee-types/{id}', [FeeTypeController::class, 'update']);
        Route::delete('fee-types/{id}', [FeeTypeController::class, 'destroy']);
        Route::post('fee-types/bulk', [FeeTypeController::class, 'bulkCreate']);
    });

    Route::get('invoice-items', [InvoiceItemController::class, 'index']);
    Route::post('invoice-items', [InvoiceItemController::class, 'store']);
    Route::get('invoice-items/{id}', [InvoiceItemController::class, 'show']);
    Route::put('invoice-items/{id}', [InvoiceItemController::class, 'update']);
    Route::delete('invoice-items/{id}', [InvoiceItemController::class, 'destroy']);
});
