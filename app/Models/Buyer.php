<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buyer extends Model
{
    use HasFactory;

    protected $fillable = ['organization_id', 'first_name', 'last_name', 'is_default', 'is_active',];
    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean',];
    protected $appends = ['full_name'];

    /**
     * Ensure a single default buyer per organization: whenever a buyer is
     * saved as the default, unset the flag on every other buyer that
     * belongs to the same organization.
     */
    protected static function booted(): void
    {
        static::saved(function (self $buyer): void {
            if (! $buyer->is_default) {
                return;
            }

            static::query()
                ->where('organization_id', $buyer->organization_id)
                ->whereKeyNot($buyer->getKey())
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

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
