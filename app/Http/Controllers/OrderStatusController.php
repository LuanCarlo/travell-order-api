<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OrderSatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $status = OrderSatus::all();
        return json_encode(['status'=>200, 'record'=>$status]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
