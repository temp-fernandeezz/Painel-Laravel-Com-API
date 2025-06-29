<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Data\OrderData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::with(['client', 'products'])
            ->where('client_id', $user->id)
            ->get();

        return response()->json($orders);
    }

    public function show(Order $order)
    {
        $user = Auth::user();

        if ($order->client_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($order->load(['client', 'products']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $data = OrderData::from($validated);

        DB::beginTransaction();

        try {
            $total = 0;
            foreach ($data->items as $item) {
                $total += $item->quantity * $item->unit_price;
            }

            $order = Order::create([
                'client_id' => $data->client_id,
                'total' => $total,
            ]);

            foreach ($data->items as $item) {
                $order->products()->attach($item->product_id, [
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);
            }

            DB::commit();

            return response()->json($order->load(['client', 'products']), 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $data = OrderData::from($validated);

        DB::beginTransaction();

        try {
            $total = 0;
            foreach ($data->items as $item) {
                $total += $item->quantity * $item->unit_price;
            }

            $order->update([
                'client_id' => $data->client_id,
                'total' => $total,
            ]);

            $order->products()->detach();

            foreach ($data->items as $item) {
                $order->products()->attach($item->product_id, [
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]);
            }

            DB::commit();

            return response()->json($order->load(['client', 'products']));
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json(['message' => 'Pedido ocultado com sucesso.']);
    }

}
