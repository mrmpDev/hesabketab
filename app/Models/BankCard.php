<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankCard extends Model
{
    protected $fillable = ['organization_id', 'bank_name', 'card_number', 'holder_name', 'is_default', 'is_active',];
    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean',];
    protected $appends = ['display_name'];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    public function getDisplayNameAttribute(): string
    {
        $last4 = substr($this->card_number, -4);
        return $this->bank_name . ' - ' . $this->holder_name . ' (' . $last4 . ')';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
