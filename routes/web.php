<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;


use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\client\CardController;



use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\client\NewsController;
use App\Http\Controllers\client\LoginController;
use App\Http\Controllers\client\UsersController;



use App\Http\Controllers\Admin\HomeAdminController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
//View Admin


Route::resource('admin-home', HomeAdminController::class);
// Route::resource('admin-products', AdminProductsController::class);
// Danh mục
// Route::resource('admin-categories', AdminCategoriesController::class);





use App\Http\Controllers\client\ClientCategories;
use App\Http\Controllers\admin\AdminNewsController;


use App\Http\Controllers\client\ProductsController;
use App\Http\Controllers\client\RegisterController;
use App\Http\Controllers\admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminOrdersController;
use App\Http\Controllers\Admin\AdminBannersController;
use App\Http\Controllers\Admin\AdminCouponsController;
use App\Http\Controllers\Admin\AdminProductsController;
use App\Http\Controllers\admin\Customeraddress;
use App\Http\Controllers\admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminUserStaffController;
use App\Http\Controllers\Admin\AdminCategoriesController;
use App\Http\Controllers\Admin\AdminCommentsController;
use App\Http\Controllers\Admin\AdminStatisticsController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\AdminUserController as ControllersAdminUserController;







Route::resource('discount', DiscountController::class);
Route::post('/apply-discount', [DiscountController::class, 'applyDiscount'])->name('apply.discount');
Route::post('/remove-discount', [DiscountController::class, 'removeDiscount'])->name('remove.discount');

// quản lí admin và nhân viên
Route::prefix('admin')->group(function () {
    Route::resource('user-staff', AdminUserStaffController::class)->middleware('admin'); // Thêm middleware vào đây

    // Đăng nhập admin và nhân viên
    Route::get('login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminLoginController::class, 'login'])->name('admin.login.submit');
    Route::post('logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

    // Mã giảm giá
    Route::resource('admin-coupons', AdminCouponsController::class);
    //  Route::get('/admin-coupons/products/{categoryId}', [AdminCouponsController::class, 'getProductsByCategory']);


Route::get('/admin-home', [HomeAdminController::class, 'index'])->name('admin-home.index');


    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::prefix('admin')->middleware('auth')->group(function () {
        Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
        Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('admin.reviews.show');
    });



    Route::get('/admin-home', [HomeAdminController::class, 'index'])->name('admin-home.index');






 
 // Route cho tin tức
 Route::resource('new', AdminNewsController::class);



    // Sản phẩm
    Route::resource('admin-products', AdminProductsController::class);

    // Danh mục
    Route::resource('admin-categories', AdminCategoriesController::class);

    // Khách hàng
    Route::resource('admin-customers', AdminUserController::class);

    // Banner
    Route::resource('admin-banners', AdminBannersController::class);

    // Đặt hàng
    Route::resource('admin-orders', AdminOrdersController::class);
    // Route để hiển thị trang duyệt đơn hàng
    Route::get('admin/orders/{id}/approve', [AdminOrdersController::class, 'approve'])->name('admin-orders.approve');
    Route::get('/admin/orders/approve', [AdminOrdersController::class, 'approveIndex'])->name('admin-orders.approve.index');
    Route::get('/admin/orders/deleted', [AdminOrdersController::class, 'deletedOrders'])->name('admin-orders.deleted');
    Route::put('/admin/orders/restore/{id}', [AdminOrdersController::class, 'restore'])->name('admin-orders.restore');
    Route::delete('/admin/orders/force-delete/{id}', [AdminOrdersController::class, 'forceDelete'])->name('admin-orders.forceDelete');
    Route::get('/admin/orders/approve',[AdminOrdersController::class,'receivedIndex'])->name('admin-orders.received');

    // Route để cập nhật trạng thái đơn hàng
    Route::put('admin/orders/{id}/update-status', [AdminOrdersController::class, 'updateStatus'])->name('admin-orders.update-status');


    // Thống kê
    Route::get('/admin/statistics', [AdminStatisticsController::class, 'index'])->name('admin.statistics');
    Route::get('/admin/statistics/fetch', [AdminStatisticsController::class, 'getStatistics'])->name('admin.statistics.fetch');


    // Route cho tin tức
    Route::resource('new', AdminNewsController::class);

    Route::resource('users', AdminUserController::class);
    //
    Route::resource('inventory', AdminInventoryController::class);
});


// dang nhap admin và nhân viên

// Route cho view client
<<<<<<< HEAD
Route::resource('/', HomeController::class);

=======
Route::resource('client-home', HomeController::class);
>>>>>>> a6b011a41e0e22aa76edd639eacf05385bfcbdc7
Route::resource('client-user', UsersController::class);
Route::resource('client-categories', ClientCategories::class);
Route::resource('client-login', LoginController::class);
// đăng xuất khách hàng
Route::post('client-logout', [LoginController::class, 'logout'])->name('client-logout');
//
Route::resource('client-register', RegisterController::class);
Route::resource('client-products', ProductsController::class);

Route::resource('client-news', NewsController::class);
Route::resource('client-card', CardController::class);

Route::resource('client-news',  NewsController::class);
Route::resource('client-card',  CardController::class);

Route::resource('admin-customers', AdminUserController::class);

Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
Route::get('/users/{id}/restore', [AdminUserController::class, 'restore'])->name('users.restore');


// Danh mục
Route::resource('admin-categories', AdminCategoriesController::class);
// Mã giảm giá
Route::resource('admin-coupons', AdminCouponsController::class);
// Đặt hàng
Route::resource('admin-orders', AdminOrdersController::class);
// in pdf
Route::get('/admin/orders/{id}/pdf', [AdminOrdersController::class, 'generatePDF'])->name('admin-orders.generatePDF');
Route::resource('admin-products', AdminProductsController::class);
//bình luận
Route::resource('admin-comments', AdminCommentsController::class);

Route::resource('admin-banners', AdminBannersController::class);


Route::resource('admin-banners', AdminBannersController::class);

Route::get('Admin/Banners/trash', [AdminBannersController::class, 'trash']);


Route::post('Admin/Banners/delete/{id}', [AdminBannersController::class, 'delete']);

Route::get('/new', [AdminNewsController::class, 'index'])->name('new.index');
Route::get('/new/create', [AdminNewsController::class, 'create'])->name('new.create');
Route::get('/new/{id}/edit', [AdminNewsController::class, 'edit'])->name('new.edit');
Route::post('/new/store', [AdminNewsController::class, 'store'])->name('new.store');
Route::put('/new/{id}/update', [AdminNewsController::class, 'update'])->name('new.update');
Route::delete('/new/{id}', [AdminNewsController::class, 'destroy'])->name('new.destroy');
Route::get('/new/show/{id}', [AdminNewsController::class, 'show'])->name('new.show');
