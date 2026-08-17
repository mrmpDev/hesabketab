<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Morilog\Jalali\Jalalian;

class Expense extends Model
{
    protected $fillable = [
        'organization_id',
        'expense_category_id',
        'buyer_id',
        'bank_card_id',
        'vendor_id',
        'payment_method',
        'expense_date',
        'total_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'total_amount' => 'integer',
    ];

    protected $appends = [
        'payment_method_label',
        'jalali_date',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    public function bankCard(): BelongsTo
    {
        return $this->belongsTo(BankCard::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ExpenseAttachment::class);
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'pos' => 'پوز',
            'transfer' => 'کارت‌به‌کارت',
            'cash' => 'نقدی',
            default => 'نامشخص',
        };
    }

    public function getJalaliDateAttribute(): ?string
    {
        return $this->expense_date
            ? Jalalian::fromDateTime($this->expense_date)->format('Y/m/d')
            : null;
    }

    public function scopeForOrganization($query, int $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }
}
