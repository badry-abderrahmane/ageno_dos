<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'org_name',
        'org_footer',
        'org_modality',
        'org_bank',
        'org_logo',
        'org_color',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
