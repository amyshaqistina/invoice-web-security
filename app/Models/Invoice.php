<?php

namespace App\Models;

use App\Models\Item;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_invoice')
            ->withPivot('quantity');
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'invoice_payments');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function recurring_invoices()
    {
        return $this->belongsTo(RecurringInvoice::class);
    }

    static function generate_invoice_number($tenant_id)
    {

        return Invoice::where('team_id', $tenant_id)->orderBy('id', 'desc')->first()?->id + 1;
    }


    static function liststatus()
    {
        return [
            'sent' => 'sent',
            'pending' => 'pending',
            'overdue' => 'overdue',
            'paid' => 'paid',
            'cancel' => 'cancel',
            'refund' => 'refund',

        ];
    }
}
