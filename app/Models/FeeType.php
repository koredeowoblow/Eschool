<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTenancyTrait;

class FeeType extends Model
{
    use HasTenancyTrait;
    protected $fillable = ['name', 'description', 'amount', 'school_id', 'session_id', 'term_id'];

    /**
     * Get the invoice items for this fee type.
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the session this fee type is assigned to.
     */
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    /**
     * Get the term this fee type is assigned to.
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
