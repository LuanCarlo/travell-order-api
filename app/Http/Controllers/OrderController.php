<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
                //$prods = Product::all();
        $orders = Order::all();
        return json_encode(['status'=>200, 'record'=>$orders]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date',
            'user_id' => 'required|integer',
        ], [
            'destination.required'  => 'O campo destino é obrigatório.',
            'departure_date.required' => 'O campo data de ida é obrigatório.',
            'return_date.required' => 'O data de volta é obrigatório.',
            'user_id.required'   => 'Ocorreu algum problema e não foi possível obter o usuário.',
        ]);

        
        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        }

        try {

            $order = Order::create($validation);

            return response()->json([
                'message' => 'Solicitação criada com sucesso!',
                'data' => $order
            ], 201);  

        } catch (Exception $e) {

            return json_encode(['status'=>400, 'msg'=>$e->getMessage()]);
        }         
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $order = Order::find($id);
        if (isset($order)) {
            return json_encode(['status'=>200, 'record'=>$order]);
        }
        return json_encode(['status'=>400, 'msg'=>'Produto não encontrado']);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validation = Validator::make($request->all(), [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date',
            'user_id' => 'required|integer',
        ], [
            'destination.required'  => 'O campo destino é obrigatório.',
            'departure_date.required' => 'O campo data de ida é obrigatório.',
            'return_date.required' => 'O data de volta é obrigatório.',
            'user_id.required'   => 'Ocorreu algum problema e não foi possível obter o usuário.',
        ]);


        
        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        }
        try {

            $order = Order::find($id);
            if (isset($order)) {
                $order->destination = $request->input('destination');
                $order->departure_date = $request->input('departure_date');
                $order->return_date = $request->input('return_date');
                $order->user_id = $request->input('user_id');

                $order->save();
                return json_encode(['status'=>200, 'record'=>$order]);
            }

            return json_encode(['status'=>400, 'msg'=>' Solicitação não encontrada']);
        } catch (Exception $e) {

            return json_encode(['status'=>400, 'msg'=>$e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
