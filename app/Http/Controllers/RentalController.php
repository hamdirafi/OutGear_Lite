<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Rental;
use App\Jobs\ProcessRentalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RentalController extends Controller
{
    public function index()
{
    $items = Item::all();
    // Ambil semua transaksi urut dari yang terbaru
    $rentals = Rental::with('item')->latest()->get(); 

    return view('welcome', compact('items', 'rentals'));
}
    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rental_rate_per_day' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:1',
        ]);

        Item::create($validated);
        return redirect()->back()->with('success', 'Barang berhasil ditambahkan!');
    }

    public function storeRental(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'customer_name' => 'required|string|max:255',
            'days' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $item = Item::findOrFail($validated['item_id']);
            
            if ($item->stock < 1) {
                throw new \Exception('Stok barang habis!');
            }

            $totalPrice = $item->rental_rate_per_day * $validated['days'];

            $rental = Rental::create([
                'item_id' => $item->id,
                'customer_name' => $validated['customer_name'],
                'days' => $validated['days'],
                'total_price' => $totalPrice,
                'status' => 'active',
            ]);

            $item->decrement('stock');

            ProcessRentalNotification::dispatch($rental);
        });

        return redirect()->back()->with('success', 'Transaksi sewa berhasil disimpan dan dikirim ke queue worker!');
    }
        public function returnItem(Rental $rental)
    {
        if (strtoupper($rental->status) === 'ACTIVE') {
            $rental->update(['status' => 'RETURNED']);

            // Kembalikan stok barang di master data
            if ($rental->item) {
                $rental->item->increment('stock');
            }
        }

        return redirect()->back()->with('success', 'Barang berhasil dikembalikan dan stok telah bertambah!');
    }
}