<?php

namespace App\Models\View;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Copy extends BaseModel
{
    protected $table = 'vw_copies';
    public $timestamps = false;

    protected $guarded = [];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
