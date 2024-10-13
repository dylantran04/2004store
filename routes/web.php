<?php





use App\Http\Controllers\client\home;



use App\Http\Controllers\AdminOrdersController;

use App\Http\Controllers\AdminCouponsController;
use App\Http\Controllers\Admin\AdminCategoriesController;

use App\Http\Controllers\Admin\AdminBannersController;
use App\Http\Controllers\Admin\AdminProductsController;
use App\Http\Controllers\admin\NewsController;
use Illuminate\Support\Facades\Route;




Route::resource('admin-products', AdminProductsController::class);


// Danh mục
Route::resource('admin-categories', AdminCategoriesController::class);
// Mã giảm giá
Route::resource('admin-coupons', AdminCouponsController::class);
// Đặt hàng
Route::resource('admin-orders', AdminOrdersController::class);


Route::resource('admin-banners',AdminBannersController::class);

Route::get('Admin/Banners/trash',[AdminBannersController::class,'trash']);

Route::post('Admin/Banners/delete/{id}',[AdminBannersController::class,'delete']);

Route::get('/new', [NewsController::class, 'index'])->name('new.index');
Route::get('/new/create', [NewsController::class, 'create'])->name('new.create');
Route::get('/new/{id}/edit', [NewsController::class, 'edit'])->name('new.edit');
Route::post('/new/store', [NewsController::class, 'store'])->name('new.store');
Route::put('/new/{id}/update', [NewsController::class, 'update'])->name('new.update');
Route::delete('/new/{id}', [NewsController::class, 'destroy'])->name('new.destroy');
Route::get('/new/show/{id}',[NewsController::class,'show'])->name('new.show');


