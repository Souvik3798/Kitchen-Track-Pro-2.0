<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Invoice extends Controller
{
    public function generateInvoice(Order $order)
    {
        if ($order->organization_id !== Auth::user()->organization_id) {
            return abort(403, 'Unauthorized action.');
        }

        // dd($order->organization_id . ',' . Auth::user()->organization_id);

        // Generate the PDF or return the view for the invoice
        return view('invoice', compact('order'));
    }
}
