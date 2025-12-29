<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function invoice($invoiceId)
    {
        $invoice = Invoice::find($invoiceId);

        if (!$invoice) {
            abort(404);
        }

        // Check authentication
        if (!auth()->check()) {
            abort(403, 'Unauthorized');
        }

        $user = auth()->user();

        // AUTHORIZATION: Superadmin can access any invoice
        // Regular users can only access their team's invoices
        $isSuperAdmin = $user->hasRole('super_admin') || $user->hasRole('admin');

        if (!$isSuperAdmin) {
            // Regular user: check if they belong to the invoice's team
            $userTeamIds = $user->team()->pluck('id')->toArray();
            if (!in_array($invoice->team_id, $userTeamIds)) {
                abort(403, 'Unauthorized');
            }
        }

        $data = [
            'invoice' => $invoice,
            'customer' => $invoice->customer,
            'items' => $invoice->items,
            'total' => $invoice->items->sum(fn($item) => $item->price * $item->quantity),
        ];

        $pdf = Pdf::loadView('pdf.invoice', $data);
        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}
