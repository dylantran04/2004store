<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Http\Controllers\Controller;
use App\Models\AdminOrder;
use App\Models\AdminProducts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminOrdersController extends Controller
{

    public function index()
    {
        $orders = AdminOrder::all();
        // Trong phương thức index
        foreach ($orders as $order) {
            // Thêm kiểm tra để chỉ hiển thị nút duyệt cho các đơn hàng chờ xử lý
            if ($order->status === 'Chờ xử lý') {
                // Hiển thị nút duyệt
            }
        }

        return view('Admin.orders.index', compact('orders'));
    }

    public function approveIndex()
    {
        // Lấy danh sách các đơn hàng có trạng thái 'Chờ xử lý'
        $orders = AdminOrder::where('status', 'Chờ xử lý')->get();
        return view('Admin.orders.approve_index', compact('orders'));
    }

    public function receivedIndex()
    {
        $orders = AdminOrder::where('status', 'Đã nhận hàng')->get();
        return view('Admin.orders.received_index', compact('orders'));
    }

    public function create()
    {
        $products = AdminProducts::all();
        // session()->put('cart_total', $order->total);
        return view('admin.orders.create', compact('products'));
    }

    public function generatePDF($id)
    {
        $order = AdminOrder::with('products')->findOrFail($id);
        session()->put('cart_total', $order->total);
        $pdf = PDF::loadView('admin.orders.pdf', compact('order'));
        return $pdf->download('order_' . $order->id . '.pdf');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'status' => 'required|string|max:50',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1',
        ]);

        $order = AdminOrder::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'total' => 0,
            'status' => $validated['status'],
        ]);


        $total = 0;
        foreach ($validated['products'] as $index => $productId) {
            $product = AdminProducts::find($productId);
            $quantity = $validated['quantities'][$index];
            $order->products()->attach($productId, ['quantity' => $quantity]);
            $total += $product->price * $quantity;
        }
        $order->update(['total' => $total]);
        session()->put('cart_total', $order->total);

        return redirect()->route('admin-orders.approve.index')->with('success', 'Đơn hàng đã được tạo thành công!');
    }


    public function show($id)
    {
        $order = AdminOrder::with('products')->findOrFail($id);
        session()->put('cart_total', $order->total);
        foreach ($order->products as $product) {
            echo Storage::url($product->image_path);
        }
        // session()->put('cart_total', $order->total);
        return view('admin.orders.show', compact('order'));
    }




    public function edit($id)
    {
        $order = AdminOrder::with('products')->findOrFail($id);
        $products = AdminProducts::all();
        return view('admin.orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, $id)
    {
        // Fetch the order first
        $order = AdminOrder::findOrFail($id);

        if ($request->has('status')) {
            $validated = $request->validate([
                'status' => 'required|string|max:50',
            ]);
            $order->update(['status' => $validated['status']]);
            return redirect()->route('admin-orders.index')->with('success', 'Trạng thái đơn hàng đã được cập nhật!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'total' => 'required|numeric',
            'status' => 'required|string|max:50',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'integer|min:1',
        ]);

        $order->update($validated);
        $order->products()->detach();
        foreach ($validated['products'] as $index => $productId) {
            $order->products()->attach($productId, ['quantity' => $validated['quantities'][$index]]);
        }
        session()->put('cart_total', $order->total);
        return redirect()->route('admin-orders.index')->with('success', 'Đơn hàng đã được cập nhật thành công!');
    }
    public function approve($id)
    {
        $order = AdminOrder::findOrFail($id);
        // $order->status = 'Đã xử lý'; // Hoặc trạng thái bạn muốn
        // $order->save();
        session()->put('cart_total', $order->total);
        return view('admin.orders.approve', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = AdminOrder::findOrFail($id);

        // Kiểm tra trạng thái và cập nhật
        if ($order->status === 'Chờ xử lý') {
            $order->update(['status' => 'Đã xử lý']);
            return redirect()->route('admin-orders.index')->with('success', 'Đơn hàng đã được duyệt thành công!');
        }

        return redirect()->route('admin-orders.index')->with('error', 'Đơn hàng không thể duyệt!');
    }
    public function destroy($id)
    {
        $order = AdminOrder::findOrFail($id);
        $order->delete();

        return redirect()->route('admin-orders.approve.index')->with('success', 'Đơn hàng đã được xóa thành công!');
    }
   
    public function restore($id)
    {
        $order = AdminOrder::onlyTrashed()->findOrFail($id);
        $order->restore();

        return redirect()->route('admin-orders.deleted')->with('success', 'Đơn hàng đã được khôi phục thành công!');
    }


    public function forceDelete($id)
    {
        $order = AdminOrder::onlyTrashed()->findOrFail($id);
        $order->forceDelete();

        return redirect()->route('admin-orders.deleted')->with('success', 'Đơn hàng đã được xóa vĩnh viễn thành công!');
    }

    // return redirect()->route('admin-orders.index')->with('error', 'Đơn hàng không thể duyệt!');


public function deletedOrders()
{
    $deletedOrders = AdminOrder::onlyTrashed()->get(); // Lấy tất cả các đơn hàng đã xóa
    return view('Admin.orders.deleted', compact('deletedOrders'));
}



public function listDonHangDaHuy(){
    $donHangBiHuy = AdminOrder::where('status', 'Hủy')
                    ->with('orderItems.product')
                    ->get();
                    // dd($donHangBiHuy);
    return view('Admin.orders.listDonHangHuy',compact('donHangBiHuy'));
}
}


