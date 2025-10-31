<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'total',
        'status',
        'client_id',
        'user_id',
    ];

    // --- Relationships ---

    /**
     * Get the client that the invoice belongs to.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the user (employee/creator) that created the invoice.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the invoice (Many-to-Many).
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'invoice_products')
            ->using(InvoiceProduct::class)
            ->withPivot(['qty', 'price']);
    }
}
