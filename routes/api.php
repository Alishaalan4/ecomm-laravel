<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Controllers
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\User\ProfileController;
use App\Http\Controllers\API\Admin\UserController as AdminUserController;
use App\Http\Controllers\API\Admin\ProductController as AdminProductController;
use App\Http\Controllers\API\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\API\Admin\CategoryController as AdminCategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


/*
    auth routes:
    POST   /api/register
    POST   /api/login
    POST   /api/logout

    user:routes
    GET    /api/user/profile
    PUT    /api/user/profile

    GET    /api/user/cart
    POST   /api/user/cart/add
    PUT    /api/user/cart/update
    DELETE /api/user/cart/remove/{id}

    POST   /api/user/checkout
    GET    /api/user/orders
    GET    /api/user/orders/{id}

    admin routes
    GET    /api/admin/users
    POST   /api/admin/products
    PUT    /api/admin/products/{id}
    DELETE /api/admin/products/{id}

    GET    /api/admin/orders
    PUT    /api/admin/orders/{id}/status

    public routes
    GET    /api/products
    GET    /api/products/{id}
    GET    /api/categories
    GET    /api/categories/{id}/products

*/



// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


// public routes : products and categories 
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/{id}', [ProductController::class, 'show']);
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{id}/products', [CategoryController::class, 'show']);
});


// auth routes : register, login, logout
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);


// user routes , protected 
Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    // profile routes
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/changePassword',[ProfileController::class,'changePassword']);
    // cart routes 
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/update', [CartController::class, 'update']);
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove']);
    Route::delete('/cart/clear', [CartController::class, 'clear']);

    // checkout routes 
    Route::post('/checkout', [OrderController::class, 'checkout']);

    // orders routes 
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});


// admin routes , protected 
Route::middleware(['auth:sanctum','admin'])->prefix('admin')->group(function () {
    // users routes 
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::get('/users/{id}', [AdminUserController::class, 'show']);
    Route::post('/users/add',[AdminUserController::class,'create']);
    Route::put('/users/{id}', [AdminUserController::class, 'update']);
    Route::post('/users/{id}/updatePassword', [AdminUserController::class, 'updatePassword']);
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy']);

    // products routes 
    Route::get('/products', [AdminProductController::class, 'index']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::put('/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);

    // orders routes 
    Route::get('/orders', [AdminOrderController::class, 'index']);
    Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
    Route::put('/orders/{id}/status', [AdminOrderController::class, 'update']);

    // category routes
    Route::get('/categories', [AdminCategoryController::class, 'index']);
    Route::get('/categories/{id}', [AdminCategoryController::class, 'show']);
    Route::post('/categories/create', [AdminCategoryController::class, 'create']);
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy']);
});
