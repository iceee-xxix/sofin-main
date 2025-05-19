<?php

use App\Http\Controllers\admin\Admin;
use App\Http\Controllers\admin\Category;
use App\Http\Controllers\admin\Menu;
use App\Http\Controllers\admin\Preview;
use App\Http\Controllers\admin\Progress;
use App\Http\Controllers\admin\Promotion;
use App\Http\Controllers\admin\Table;
use App\Http\Controllers\admin\Rider;
use App\Http\Controllers\admin\Stock;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Delivery;
use App\Http\Controllers\Main;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
//สั่งจากที่ร้าน
Route::get('/buy', function () {
    return view('users.list_page');
});
Route::get('/total', function () {
    return view('index');
});
//สั่ง delivery
Route::get('/', [Delivery::class, 'index'])->name('index');
Route::get('/users/login', [Delivery::class, 'login'])->name('users.login');
Route::get('/users/register', [Delivery::class, 'register'])->name('users.register');
Route::post('/users/UsersRegister', [Delivery::class, 'UsersRegister'])->name('users.UsersRegister');
Route::get('/users/detail/{id}', [Delivery::class, 'detail'])->name('users.detail');
Route::get('/users/preview/{id}', [Delivery::class, 'preview'])->name('users.preview');
Route::get('/users/preview/preview_detail/{id}', [Delivery::class, 'preview_detail'])->name('users.preview_detail');
Route::get('/users/order', [Delivery::class, 'order'])->name('users.order');
Route::post('/users/sendEmp', [Delivery::class, 'sendEmp'])->name('users.sendEmp');
Route::post('/users/sendorder', [Delivery::class, 'SendOrder'])->name('users.SendOrder');

Route::middleware(['role:user'])->group(function () {
    Route::get('/users/users', [Delivery::class, 'users'])->name('users.users');
    Route::post('/users/usersSave', [Delivery::class, 'usersSave'])->name('users.usersSave');
    Route::get('/users/createaddress', [Delivery::class, 'createaddress'])->name('users.createaddress');
    Route::get('/users/editaddress/{id}', [Delivery::class, 'editaddress'])->name('users.editaddress');
    Route::post('/users/addressSave', [Delivery::class, 'addressSave'])->name('users.addressSave');
    Route::post('/users/change', [Delivery::class, 'change'])->name('users.change');
    Route::get('/users/listorder', [Delivery::class, 'listorder'])->name('users.listorder');
    Route::post('/users/listOrderDetail', [Delivery::class, 'listOrderDetail'])->name('users.listOrderDetail');
    Route::get('/users/pay/{id}', [Delivery::class, 'pay'])->name('users.pay');
    Route::post('/users/pay/paySave', [Delivery::class, 'paySave'])->name('users.paySave');
    Route::get('/users/progresslist', [Delivery::class, 'progresslist'])->name('users.progresslist');
});
//admin
Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/admin/auth', [AuthController::class, 'login']);
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['checkLogin'])->name('admin');

Route::middleware('checkLogin')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', [Admin::class, 'dashboard'])->name('dashboard');
    //datatable Order
    Route::post('/admin/order/listData', [Admin::class, 'ListOrder'])->name('ListOrder');
    Route::post('/admin/order/listOrderDetail', [Admin::class, 'listOrderDetail'])->name('listOrderDetail');
    Route::post('/admin/order/paymentConfirm', [Admin::class, 'paymentConfirm'])->name('paymentConfirm');
    Route::post('/admin/order/confirm_pay', [Admin::class, 'confirm_pay'])->name('confirm_pay');
    Route::post('/admin/order/confirm_rider', [Admin::class, 'confirm_rider'])->name('confirm_rider');
    //ตั้งค่าเว็บไซต์
    Route::get('/admin/config', [Admin::class, 'config'])->name('config');
    Route::post('/admin/config/save', [Admin::class, 'ConfigSave'])->name('ConfigSave');
    //โปรโมชั่น
    Route::get('/admin/promotion', [Promotion::class, 'promotion'])->name('promotion');
    Route::post('/admin/promotion/listData', [Promotion::class, 'promotionlistData'])->name('promotionlistData');
    Route::get('/admin/promotion/create', [Promotion::class, 'promotionCreate'])->name('promotionCreate');
    Route::post('/admin/promotion/save', [Promotion::class, 'promotionSave'])->name('promotionSave');
    Route::post('/admin/promotion/delete', [Promotion::class, 'promotionDelete'])->name('promotionDelete');
    Route::post('/admin/promotion/status', [Promotion::class, 'changeStatusPromotion'])->name('changeStatusPromotion');
    Route::get('/admin/promotion/edit/{id}', [Promotion::class, 'promotionEdit'])->name('promotionEdit');
    //จัดการโต้ะและเพิ่ม Qr code
    Route::get('/admin/table', [Table::class, 'table'])->name('table');
    Route::post('/admin/table/listData', [Table::class, 'tablelistData'])->name('tablelistData');
    Route::post('/admin/table/QRshow', [Table::class, 'QRshow'])->name('QRshow');
    Route::get('/admin/table/create', [Table::class, 'tableCreate'])->name('tableCreate');
    Route::get('/admin/table/edit/{id}', [Table::class, 'tableEdit'])->name('tableEdit');
    Route::post('/admin/table/delete', [Table::class, 'tableDelete'])->name('tableDelete');
    Route::post('/admin/table/save', [Table::class, 'tableSave'])->name('tableSave');
    //หมวดหมู่
    Route::get('/admin/category', [Category::class, 'category'])->name('category');
    Route::post('/admin/category/listData', [Category::class, 'categorylistData'])->name('categorylistData');
    Route::get('/admin/category/create', [Category::class, 'CategoryCreate'])->name('CategoryCreate');
    Route::get('/admin/category/edit/{id}', [Category::class, 'CategoryEdit'])->name('CategoryEdit');
    Route::post('/admin/category/delete', [Category::class, 'CategoryDelete'])->name('CategoryDelete');
    Route::post('/admin/category/save', [Category::class, 'CategorySave'])->name('CategorySave');
    //ไรเดอร์
    Route::get('/admin/rider', [Rider::class, 'rider'])->name('rider');
    Route::post('/admin/rider/listData', [Rider::class, 'riderlistData'])->name('riderlistData');
    Route::get('/admin/rider/create', [Rider::class, 'riderCreate'])->name('riderCreate');
    Route::get('/admin/rider/edit/{id}', [Rider::class, 'riderEdit'])->name('riderEdit');
    Route::post('/admin/rider/delete', [Rider::class, 'riderDelete'])->name('riderDelete');
    Route::post('/admin/rider/save', [Rider::class, 'riderSave'])->name('riderSave');
    Route::post('/admin/order/Riderconfirm_pay', [Rider::class, 'Riderconfirm_pay'])->name('Riderconfirm_pay');
    //จัดการโต้ะและเพิ่ม Qr code
    Route::get('/admin/OrderRider', [Rider::class, 'OrderRider'])->name('OrderRider');
    Route::post('/admin/OrderRider/listData', [Rider::class, 'OrderRiderlistData'])->name('OrderRiderlistData');

    //เมนูอาหาร
    Route::get('/admin/menu', [Menu::class, 'menu'])->name('menu');
    Route::post('/admin/menu/menulistData', [Menu::class, 'menulistData'])->name('menulistData');
    Route::get('/admin/menu/create', [Menu::class, 'MenuCreate'])->name('MenuCreate');
    Route::get('/admin/menu/edit/{id}', [Menu::class, 'menuEdit'])->name('menuEdit');
    Route::post('/admin/menu/delete', [Menu::class, 'menuDelete'])->name('menuDelete');
    Route::post('/admin/menu/save', [Menu::class, 'menuSave'])->name('menuSave');
    //พรีวิวหมวดหมู่
    Route::get('/admin/preview', [Preview::class, 'preview'])->name('preview');
    Route::post('/admin/preview/previewlistData', [Preview::class, 'previewlistData'])->name('previewlistData');
    Route::get('/admin/preview/create', [Preview::class, 'previewCreate'])->name('previewCreate');
    Route::get('/admin/preview/edit/{id}', [Preview::class, 'previewEdit'])->name('previewEdit');
    Route::post('/admin/preview/delete', [Preview::class, 'previewDelete'])->name('previewDelete');
    Route::post('/admin/preview/save', [Preview::class, 'previewSave'])->name('previewSave');
    //กำหนดราคาอาหาร
    Route::get('/admin/menuOption/{id}', [Menu::class, 'menuOption'])->name('menuOption');
    Route::post('/admin/menu/menulistOption', [Menu::class, 'menulistOption'])->name('menulistOption');
    Route::get('/admin/menu/menulistOptionCreate/{id}', [Menu::class, 'menulistOptionCreate'])->name('menulistOptionCreate');
    Route::post('/admin/menu/menuOptionSave', [Menu::class, 'menuOptionSave'])->name('menuOptionSave');
    Route::post('/admin/menu/menuOptionUpdate', [Menu::class, 'menuOptionUpdate'])->name('menuOptionUpdate');
    Route::get('/admin/menu/menuOptionEdit/{id}', [Menu::class, 'menuOptionEdit'])->name('menuOptionEdit');
    //สต็อกสินค้า
    Route::get('/admin/stock', [Stock::class, 'stock'])->name('stock');
    Route::post('/admin/stock/stocklistData', [Stock::class, 'stocklistData'])->name('stocklistData');
    Route::get('/admin/stock/create', [Stock::class, 'stockCreate'])->name('stockCreate');
    Route::post('/admin/stock/save', [Stock::class, 'stockSave'])->name('stockSave');
    Route::get('/admin/stock/edit/{id}', [Stock::class, 'stockEdit'])->name('stockEdit');
    Route::post('/admin/stock/delete', [Stock::class, 'stockDelete'])->name('stockDelete');
    //ผูกสต็อก
    Route::get('/admin/stock/menuOptionStock/{id}', [Stock::class, 'menuOptionStock'])->name('menuOptionStock');
    Route::post('/admin/stock/menustocklistData', [Stock::class, 'menustocklistData'])->name('menustocklistData');
    Route::get('/admin/stock/menustockCreate/{id}', [Stock::class, 'menustockCreate'])->name('menustockCreate');
    Route::get('/admin/stock/menuStockedit/{id}', [Stock::class, 'menuStockedit'])->name('menuStockedit');
    Route::post('/admin/stock/menustockSave', [Stock::class, 'menustockSave'])->name('menustockSave');
    Route::post('/admin/stock/menustockDelete', [Stock::class, 'menustockDelete'])->name('menustockDelete');
    Route::get('/admin/stock/stockDetail/{id}', [Stock::class, 'stockDetail'])->name('stockDetail');
    //พัฒนาการ
    Route::get('/admin/progress', [Progress::class, 'progress'])->name('progress');
    Route::post('/admin/progress/listData', [Progress::class, 'progresslistData'])->name('progresslistData');
    Route::get('/admin/progress/progressDetail/{id}', [Progress::class, 'progressDetail'])->name('progressDetail');
    Route::post('/admin/progress/progressDetaillistData', [Progress::class, 'progressDetaillistData'])->name('progressDetaillistData');
    Route::get('/admin/progress/progressDetail/create/{id}', [Progress::class, 'progressDetailCreate'])->name('progressDetailCreate');
    Route::get('/admin/progress/progressDetail/edit/{id}', [Progress::class, 'progressDetailEdit'])->name('progressDetailEdit');
    Route::post('/admin/progress/progressDetail/save', [Progress::class, 'ProgressSave'])->name('ProgressSave');
    // Route::post('/admin/table/QRshow', [Table::class, 'QRshow'])->name('QRshow');
    Route::post('/admin/progress/delete', [Progress::class, 'ProgressDelete'])->name('ProgressDelete');
});


require __DIR__ . '/auth.php';
