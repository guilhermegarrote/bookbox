<?php

namespace App\Models\View;

use App\Models\Copy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends BaseModel
{
    protected $table = 'vw_loans';
    public $timestamps = false;

    protected $guarded = [];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function copy(): BelongsTo
    {
        return $this->belongsTo(Copy::class);
    }
}
