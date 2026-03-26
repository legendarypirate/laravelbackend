<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;
use App\Models\InvoiceProfile;
use App\Models\InvoiceProfileBank;
use App\Traits\Loggable;

class Invoice extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    protected $fillable = [
        'uuid',
        'invoice_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'items',
        'notes',
        'payment_method',
        'payment_date',
        'paid_at',
        'resolution_notes',
        'issuer_profile_id',
        'issuer_bank_account_id',
        'issuer_profile_snapshot',
        'issuer_bank_snapshot',
        'pdf_url',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'items' => 'array',
        'issuer_profile_snapshot' => 'array',
        'issuer_bank_snapshot' => 'array',
    ];

    protected $dates = [
        'invoice_date',
        'due_date',
        'payment_date',
        'paid_at',
        'deleted_at'
    ];

    // Append accessors to array/JSON output
    protected $appends = [
        'formatted_date',
        'formatted_due_date',
        'formatted_payment_date',
        'formatted_paid_at',
        'formatted_total',
        'days_remaining',
        'items_array'
    ];

    /**
     * Format date consistently
     */
    private function formatDateForAccessor($date)
    {
        if (!$date) {
            return '';
        }

        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }

        if (is_string($date) && str_contains($date, 'T')) {
            return substr($date, 0, 10);
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return (string) $date;
        }
    }

    /**
     * Accessor for formatted invoice date (YYYY-MM-DD)
     */
    public function getFormattedDateAttribute()
    {
        return $this->formatDateForAccessor($this->invoice_date);
    }

    /**
     * Accessor for formatted due date (YYYY-MM-DD)
     */
    public function getFormattedDueDateAttribute()
    {
        return $this->formatDateForAccessor($this->due_date);
    }

    /**
     * Accessor for formatted payment date (YYYY-MM-DD)
     */
    public function getFormattedPaymentDateAttribute()
    {
        return $this->formatDateForAccessor($this->payment_date);
    }

    /**
     * Accessor for formatted paid at date (YYYY-MM-DD)
     */
    public function getFormattedPaidAtAttribute()
    {
        if (!$this->paid_at) {
            return '';
        }
        
        try {
            return Carbon::parse($this->paid_at)->format('Y-m-d');
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Accessor for items (decodes JSON automatically)
     */
    public function getItemsAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        return json_decode($value, true) ?: [];
    }

    /**
     * Mutator for items (encodes to JSON automatically)
     */
    public function setItemsAttribute($value)
    {
        $this->attributes['items'] = json_encode($value);
    }

    /**
     * Alias for items (backward compatibility)
     */
    public function getItemsArrayAttribute()
    {
        return $this->items;
    }

    /**
     * Get formatted total amount
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2);
    }

    /**
     * Get days remaining until due date
     */
    public function getDaysRemainingAttribute()
    {
        if ($this->status != 'pending') {
            return 0;
        }
        
        try {
            $dueDate = Carbon::parse($this->due_date);
            return now()->diffInDays($dueDate, false);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Scope for pending invoices
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope for cancelled invoices
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for invoices by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->status == 'pending' && $this->due_date < now();
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($paymentMethod = null, $paymentDate = null)
    {
        $this->update([
            'status' => 'paid',
            'payment_method' => $paymentMethod ?? $this->payment_method,
            'payment_date' => $paymentDate ?? now(),
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark invoice as overdue
     */
    public function markAsOverdue()
    {
        $this->update(['status' => 'overdue']);
    }

    /**
     * Calculate totals from items
     * Treats price as the total amount (with VAT) for the line item
     * Note: Frontend converts unit price * quantity to line total before submission
     */
    public static function calculateTotals($items, $taxPercent = 10)
    {
        $taxMultiplier = 1 + ($taxPercent / 100); // e.g., 1.1 for 10% VAT
        
        $subtotal = collect($items)->sum(function($item) use ($taxMultiplier) {
            // price is the total amount (with VAT) for the line item
            $totalWithVat = floatval($item['price'] ?? 0);
            // Calculate subtotal for this line: totalWithVat / (1 + taxPercent/100)
            return $totalWithVat / $taxMultiplier;
        });

        $tax = $subtotal * ($taxPercent / 100);
        $total = $subtotal + $tax;

        return [
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'total' => round($total, 2)
        ];
    }

    /**
     * Generate invoice number
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV-';
        $date = now();
        $year = $date->format('y');
        $month = $date->format('m');
        
        // Get the last invoice number for this month
        $lastInvoice = self::where('invoice_number', 'like', $prefix . $year . $month . '-%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -3);
            $sequence = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $sequence = '001';
        }
        
        return $prefix . $year . $month . '-' . $sequence;
    }

    /**
     * Get the status as text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Хүлээгдэж буй',
            'paid' => 'Төлөгдсөн',
            'overdue' => 'Хугацаа хэтэрсэн',
            'cancelled' => 'Цуцлагдсан',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get the status badge class
     */
    public function getStatusClassAttribute()
    {
        $classes = [
            'pending' => 'warning',
            'paid' => 'success',
            'overdue' => 'danger',
            'cancelled' => 'secondary',
        ];
        
        return $classes[$this->status] ?? 'info';
    }

    public function issuerProfile()
    {
        return $this->belongsTo(InvoiceProfile::class, 'issuer_profile_id');
    }

    public function issuerBankAccount()
    {
        return $this->belongsTo(InvoiceProfileBank::class, 'issuer_bank_account_id');
    }

    /**
     * Boot the model and generate UUID on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->uuid)) {
                $invoice->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    /**
     * Find invoice by UUID
     */
    public static function findByUuid($uuid)
    {
        return static::where('uuid', $uuid)->first();
    }
}