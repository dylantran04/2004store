<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Trường id tự động tăng
            $table->string('name', 255); // Tên khách hàng
            $table->string('email', 255); // Email của khách hàng
            $table->string('phone', 255); // Số điện thoại khách hàng
            $table->text('address'); // Địa chỉ giao hàng
            $table->decimal('total', 10, 2); // Tổng số tiền đơn hàng
            $table->string('status', 255); // Trạng thái đơn hàng
            $table->timestamps(); // Tạo trường created_at và updated_at
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
