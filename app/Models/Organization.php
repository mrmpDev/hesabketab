<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    protected $fillable = ['name', 'code', 'is_active', 'description',];
    protected $casts = ['is_active' => 'boolean',];

    public function buyers(): HasMany
    {
        return $this->hasMany(Buyer::class);
    }

    public function bankCards(): HasMany
    {
        return $this->hasMany(BankCard::class);
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    public function expenseCategories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function userPreferences(): HasMany
    {
        return $this->hasMany(UserPreference::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
