<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = User::find($userId);

        $query = DB::table('order')
        ->join('order_status', 'order.order_status_id', '=', 'order_status.id')
        ->join('users', 'order.user_id', '=', 'users.id')
        ->select(['order.*', 'order_status.status', 'users.name as user',
            DB::raw("DATE_FORMAT(departure_date, '%d/%m/%Y %H:%i:%s') AS departure_date"),
            DB::raw("DATE_FORMAT(return_date, '%d/%m/%Y %H:%i:%s') AS return_date"),
        ]);

        $filterStatusId = $request->query('filterStatus');
        $filterDestination = $request->query('filterDestination');
        $filterDateStart = $request->query('filterDateStart');
        $filterDateEnd = $request->query('filterDateEnd');

        if ($user && $user->admin != 1) {
            $query->where('order.user_id', $userId);
        }

        if (!empty($filterStatusId) && $filterStatusId != 'null') {
            $query->where('order.order_status_id', $filterStatusId);
        }
        if (!empty($filterDestination)) {
            $query->where('order.destination', 'LIKE', '%' . $filterDestination . '%');
        }
        if (!empty($filterDateStart)) {
            $query->where(function ($q) use ($filterDateStart) {
                $q->whereDate('order.departure_date', '>=', $filterDateStart);
                $q->orWhere('order.created_at', '>=', $filterDateStart);
            });
        }
        if (!empty($filterDateEnd)) {
            $query->where(function ($q) use ($filterDateEnd) {
                $q->whereDate('order.departure_date', '<=', $filterDateEnd);
                $q->orWhere('order.created_at', '<=', $filterDateEnd);
            });
        }

        $orders = $query->get();

        return json_encode(['status'=>200, 'record'=>$orders, 'usuario'=>$user]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date', 
            'user_id' => 'required|integer',
        ], [
            'destination.required'  => 'O campo destino é obrigatório.',
            'destination.max' => 'O campo destino não pode ter mais de 255 caracteres.',
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
            
            $validatedData = $validation->validated(); 

            $order = Order::create($validatedData);

            return response()->json([
                'message' => 'Solicitação criada com sucesso!',
                'data' => $order,
                'status' => 201
            ], 201); 

        } catch (Exception $e) {
            return response()->json([
                'status' => 500, 
                'msg' => 'Erro ao salvar o pedido: ' . $e->getMessage()
            ], 500); 
        } 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $order = Order::with('user')->find($id);
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
        $validation = Validator::make($request->all(), [
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date', 
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

            $user = Auth::user();

            if (isset($order)) {

                if ((!$user || $user->admin != 1) && ($order->order_status_id != $request->order_status_id)) {
                    abort(403, 'Acesso negado. Você não tem permissão para alterar status.');
                }

                $order->destination = $request->destination;
                $order->departure_date = $request->departure_date;
                $order->return_date = $request->return_date;
                $order->user_id = $request->user_id;

                if ($order->order_status_id == 2 && (int) $request->order_status_id != 2) {
                    abort(403, 'Você não pode alterar o status de um pedido já aprovado.');
                }

                $order->order_status_id = (int) $request->order_status_id;

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

    /**
     * Update the specified resource in storage.
     */
    public function updateStatusOrder(Request $request, string $id)
    {
        try {

            $user = Auth::user();

            if (!$user || $user->admin != 1) {
                abort(403, 'Acesso negado. Você não tem permissão para alterar status.');
            }
            
            $order = Order::find($id);
            if (isset($order)) {
              
                if ($order->order_status_id == 2 && (int) $request->order_status_id != 2) {
                    abort(403, 'Você não pode alterar o status de um pedido já aprovado.');
                }
                $order->order_status_id = (int) $request->status;

                $order->save();

                try {
                    Mail::to($order->user->email)->send(new OrderStatusUpdated($order));
                } catch (\Exception $e) {
                    \Log::error('Falha ao enviar e-mail de status do pedido #' . $order->id . ': ' . $e->getMessage());
                }

                return json_encode(['status'=>200, 'record'=>$order]);
            }

            return json_encode(['status'=>400, 'msg'=>' Solicitação não encontrada']);
        } catch (Exception $e) {

            return json_encode(['status'=>400, 'msg'=>$e->getMessage()]);
        }
    }
}
