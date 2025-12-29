<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringInvoice extends Model
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
        return $this->belongsToMany(Item::class, 'item_recurringinvoices')
        ->withPivot('quantity');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public static function frequencies()
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'yearly' => 'Yearly',
        ];
    }

    public static function status()
    {
        return [
            'on_hold' => 'On Hold',
            'active' => 'Active',
            'completed' => 'Completed',

        ];
    }

    public static function next_invoice_date($frequency = 'monthly', $start_date = null)
    {
        // Set default value for start_date
        $start_date = $start_date ? Carbon::parse($start_date) : Carbon::now();

        // Handle each frequency type
        switch ($frequency) {
            case 'daily':
                return $start_date->addDay()->format('Y-m-d');
            case 'weekly':
                return $start_date->addWeek()->format('Y-m-d');
            case 'monthly':
                return $start_date->addMonth()->format('Y-m-d');
            case 'yearly':
                return $start_date->addYear()->format('Y-m-d');
            default:
                // Default to monthly if no valid frequency is given
                return $start_date->addMonth()->format('Y-m-d');
        }
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }



    static function generate_invoice($recurring_invoice_id, $date = null)
    {
        $date = $date ?? Carbon::now()->format('Y-m-d');
    
        $recurring_invoice = RecurringInvoice::find($recurring_invoice_id);
    
        if (!$recurring_invoice) {
            return;
        }

        // dd($recurring_invoice->start_date, $date, $recurring_invoice->limit_by, $recurring_invoice->invoices?->count(), $recurring_invoice->limit_by);
        if (
            $recurring_invoice->next_invoice_date <= $date &&
            ($recurring_invoice->limit_by == 0 || $recurring_invoice->invoices?->count() < $recurring_invoice->limit_by)
        ) {
    
            $invoice = Invoice::create([
                'customer_id' => $recurring_invoice->customer_id,
                'date' => $date,
                'due_date' => self::next_invoice_date($recurring_invoice->frequency, $date),
                'invoice_number' => Invoice::generate_invoice_number($recurring_invoice->team_id),
                'discount' => 0,
                'notes' => $recurring_invoice->notes,
                'team_id' => $recurring_invoice->team_id,
                'recurring_invoice_id' => $recurring_invoice->id,
            ]);
    
            foreach ($recurring_invoice->items as $item) {
                $invoice->items()->attach($item->id, [
                    'quantity' => $item->pivot->quantity,
                ]);
            }

            //update next invoice date
            $recurring_invoice->update([
                'next_invoice_date' =>  self::next_invoice_date($recurring_invoice->frequency, $date)
            ]);
        }
    }
}
