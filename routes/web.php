<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MovingQuoteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

Route::get('/legals', function () {
    return view('legal');
});
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard',[DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/api/moving-quotes/{movingQuote}', [MovingQuoteController::class, 'update']);
});


Route::get('/run-maintenance', function (\Illuminate\Http\Request $request) {
    $secret = $request->query('key'); // ?key=...
    $expected = 'gabo1234';

    if (!$secret || $secret !== $expected) {
        return response()->json([
            'success' => false,
            'message' => 'No autorizado'
        ], 401);
    }

    $results = [];

    // migrate
    try {
        Artisan::call('migrate', ['--force' => true]);
        $results['migrate'] = [
            'success' => true,
            'output' => Artisan::output(),
        ];
    } catch (\Throwable $e) {
        $results['migrate'] = [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }

    // db:seed
    try {
        Artisan::call('db:seed', ['--force' => true]);
        $results['db_seed'] = [
            'success' => true,
            'output' => Artisan::output(),
        ];
    } catch (\Throwable $e) {
        $results['db_seed'] = [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }

    // key:generate
    try {
        Artisan::call('key:generate', ['--force' => true]);
        $results['key_generate'] = [
            'success' => true,
            'output' => Artisan::output(),
        ];
    } catch (\Throwable $e) {
        $results['key_generate'] = [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }

    // opcional limpiar cachés
    try {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    } catch (\Throwable $e) {
        // no es crítico
    }

    // determinar si todo pasó bien
    $allOk = collect($results)->every(fn ($r) => $r['success'] === true);

    return response()->json([
        'success' => $allOk,
        'message' => $allOk ? 'Comandos ejecutados correctamente.' : 'Algunos comandos fallaron.',
        'results' => $results,
    ]);
});

require __DIR__.'/auth.php';
