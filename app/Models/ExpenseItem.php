<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseItem extends Model
{
    protected $fillable = ['expense_id', 'title', 'quantity', 'unit', 'amount',];
    protected $casts = ['quantity' => 'decimal:2', 'amount' => 'integer',];

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
