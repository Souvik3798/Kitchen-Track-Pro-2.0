<?php

use App\Filament\Owner\Resources\StockResource\Pages\MoveStock;
use App\Http\Controllers\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/owner');
});

Route::get('/invoice/{order}', [Invoice::class, 'generateInvoice'])
    ->name('invoice.generate');
