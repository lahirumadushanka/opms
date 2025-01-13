<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Jobs\SendSubscriptionToThirdParty;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'basket' => 'required|array',
            'basket.*.name' => 'required|string',
            'basket.*.type' => 'required|string',
            'basket.*.price' => 'required|numeric',
        ], [
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a valid string.',
            
            'last_name.required' => 'Last name is required.',
            'last_name.string' => 'Last name must be a valid string.',
            
            'address.required' => 'Address is required.',
            'address.string' => 'Address must be a valid string.',
            
            'basket.required' => 'Basket is required.',
            'basket.array' => 'Basket must be an array.',            
            
            'basket.*.name.required' => 'Each item must have a name.',
            'basket.*.name.string' => 'Each item name must be a valid string.',
            
            'basket.*.type.required' => 'Each item must have a type.',
            'basket.*.type.string' => 'Each item type must be a valid string.',
            
            'basket.*.price.required' => 'Each item must have a price.',
            'basket.*.price.numeric' => 'Each item price must be a number.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 406);
        }

        try {
            DB::beginTransaction();    
            
            $order = Order::create([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'address' => $request->input('address'),
            ]);

            foreach ($request->input('basket') as $item) {
                $order->orderitems()->create([
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'price' => $item['price'],
                ]);

                if ($item['type'] == 'subscription') {
                    SendSubscriptionToThirdParty::dispatch($item);
                }
            }

            DB::commit();
            
            return response()->json([
                'message' => 'Order created successfully!',
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
    
            // Return error response
            return response()->json([
                'message' => 'Failed to create the order.',
                'error' => $e->getMessage(),
            ], 500);
        }


    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
