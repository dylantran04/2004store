<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\AdminOrder;
use App\Models\User;
use Illuminate\Http\Request;

class ClientOrderControler extends Controller
{
    public function listOrder($userId)
    {
        $userOrder = User::with([
            'orders' => function ($query) {
                $query->where('status', '!=', 'Hủy');
            },
            'addresses',
            'orders.orderItems.variation.size',
            'orders.orderItems.variation.color'
        ])->get();
    
        return view('Client.ClientOrders.index',compact('userOrder'));
    }
    public function cancel($id)
{
    $order = AdminOrder::findOrFail($id);
    $order->status = 'Hủy';
    $order->save();

    return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
}
    public function show($id)
    {
        $order = AdminOrder::with([
            'orderItems.variation.size',
            'orderItems.variation.color',
            'user',
            'address'
        ])->findOrFail($id);
            
        if ($order->status == 'Chờ xử lý' && !$order->pending_time) {
            $order->pending_time = now();
        }
        
        if ($order->status == 'Đang xử lý' && !$order->processing_time) {
            $order->processing_time = now();
        }
        
        if ($order->status == 'Đang giao hàng' && !$order->shipping_time) {
            $order->shipping_time = now();
        }
        
        if ($order->status == 'Hoàn thành' && !$order->completed_time) {
            $order->completed_time = now();
        }
        
        $order->save();
        

        return view('Client.ClientOrders.show', compact('order'));
    }
    public function canceledOrders($id)
    {
        // Lấy tất cả các đơn hàng có trạng thái "Hủy"
        $order = AdminOrder::findOrFail($id);
        $canceledOrders = AdminOrder::where('status', 'Hủy')
                                ->with('orderItems.product', 'orderItems.variation') // Eager load các liên kết cần thiết
                                ->get();
    
        // Truyền dữ liệu vào view
        return view('Admin.orders.listDonHangHuy', compact('canceledOrders'));
    }


}
