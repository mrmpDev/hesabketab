<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ExpenseAttachment extends Model
{
    protected $fillable = ['expense_id', 'file_path', 'file_name', 'mime_type'];

    protected static function booted(): void
    {
        static::creating(function (self $attachment) {
            if (! empty($attachment->file_path)) {
                $attachment->file_name ??= basename($attachment->file_path);
                $attachment->mime_type ??= Storage::disk('public')->mimeType($attachment->file_path);
            }
        });
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
