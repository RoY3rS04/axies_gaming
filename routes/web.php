<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Database\Query\Builder;
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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    $collections = \App\Models\Collection::query()->has('items', '>=', 1)
        ->with(['items.media', 'likes', 'user'])
        ->get();

    $users = \App\Models\User::all();
    $items = \App\Models\Item::query()->with(['user', 'collection', 'likes', 'media'])->get();

    return view('home.index', ['items' => $items, 'collections' => $collections, 'users' => $users]);
})->middleware(['auth', 'verified'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('main', MainController::class);

Route::resource('categories', CategoryController::class);

Route::resource('collections', CollectionController::class);

Route::resource('items', ItemController::class);

Route::resource('users', UserController::class);

Route::post('/home/{item}', [LikeController::class, 'store']);

require __DIR__.'/auth.php';
