<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankCard extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'bank_name', 'card_number', 'holder_name', 'is_default', 'is_active',];
    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean',];
    protected $appends = ['display_name'];

    /**
     * Ensure a single default bank card per organization: whenever a card is
     * saved as the default, unset the flag on every other card that
     * belongs to the same organization.
     */
    protected static function booted(): void
    {
        static::saved(function (self $bankCard): void {
            if (! $bankCard->is_default) {
                return;
            }

            static::query()
                ->where('organization_id', $bankCard->organization_id)
                ->whereKeyNot($bankCard->getKey())
                ->where('is_default', true)
                ->update(['is_default' => false]);
        });
    }

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
