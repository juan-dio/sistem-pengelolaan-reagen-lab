<?php

use App\Http\Controllers\ActivityLogController;
use App\Models\Supplier;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JenisController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HakAksesController;
use App\Http\Controllers\LaporanBarangKeluarController;
use App\Http\Controllers\LaporanBarangMasukController;
use App\Http\Controllers\LaporanStokController;
use App\Http\Controllers\ManajemenUserController;
use App\Http\Controllers\UbahPasswordController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\LaporanStokOpnameController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StokAdjustmentController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\VerifikasiController;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;

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


Route::middleware('auth')->group(function () {

    Route::group(['middleware' => 'checkRole:superadmin,kepala gudang,admin gudang'], function(){
        Route::get('/', [DashboardController::class, 'index']);
        Route::resource('/dashboard', DashboardController::class);

        Route::get('/barang/get-data', [BarangController::class, 'getDataBarang']);
        Route::get('/jenis-barang/get-data', [JenisController::class, 'getDataJenisBarang']);
        Route::get('/satuan-barang/get-data', [SatuanController::class, 'getDataSatuanBarang']);
        Route::get('/supplier/get-data', [SupplierController::class, 'getDataSupplier']);
        Route::get('/alat/get-data', [AlatController::class, 'getDataAlat']);
        
        Route::get('/laporan-stok/get-data', [LaporanStokController::class, 'getData']);
        Route::get('/laporan-stok/print-stok', [LaporanStokController::class, 'printStok']);
        Route::resource('/laporan-stok', LaporanStokController::class);
        
        Route::get('/laporan-barang-masuk/get-data', [LaporanBarangMasukController::class, 'getData']);
        Route::get('/laporan-barang-masuk/print-barang-masuk', [LaporanBarangMasukController::class, 'printBarangMasuk']);
        Route::resource('/laporan-barang-masuk', LaporanBarangMasukController::class);
    
        Route::get('/laporan-barang-keluar/get-data', [LaporanBarangKeluarController::class, 'getData']);
        Route::get('/laporan-barang-keluar/print-barang-keluar', [LaporanBarangKeluarController::class, 'printBarangKeluar']);
        Route::resource('/laporan-barang-keluar', LaporanBarangKeluarController::class);

        Route::get('/laporan-stok-opname', [LaporanStokOpnameController::class, 'index']);
        Route::get('/laporan-stok-opname/get-data', [LaporanStokOpnameController::class, 'getData']);
        Route::get('/laporan-stok-opname/print-stok-opname', [LaporanStokOpnameController::class, 'printStokOpname']);

        Route::get('/ubah-password', [UbahPasswordController::class,'index']);
        Route::POST('/ubah-password', [UbahPasswordController::class, 'changePassword']);
    });

    Route::group(['middleware' => 'checkRole:superadmin,kepala gudang'], function(){
        Route::resource('/aktivitas-user', ActivityLogController::class);
    });

    Route::group(['middleware' => 'checkRole:superadmin,admin gudang'], function(){
        Route::resource('/barang', BarangController::class);

        Route::post('/barang/excel', [BarangController::class, 'readExcel']);
        Route::get('/barang/excel', [BarangController::class, 'downloadExcelTemplate']);
    
        Route::resource('/jenis-barang', JenisController::class);
    
        Route::resource('/satuan-barang', SatuanController::class);
    
        Route::resource('/supplier', SupplierController::class);
    
        Route::resource('/alat', AlatController::class);
    
        Route::get('/order/get-autocomplete-data', [OrderController::class, 'getAutoCompleteData']);
        Route::get('/order/get-data', [OrderController::class, 'getDataOrder']);
        Route::resource('/order', OrderController::class);

        Route::get('/barang-masuk/get-autocomplete-data', [BarangMasukController::class, 'getAutoCompleteData']);
        Route::get('/barang-masuk/get-data', [BarangMasukController::class, 'getDataBarangMasuk']);
        Route::resource('/barang-masuk', BarangMasukController::class);
    
        Route::get('/barang-keluar/get-autocomplete-data', [BarangKeluarController::class, 'getAutoCompleteData']);
        Route::get('/barang-keluar/get-data', [BarangKeluarController::class, 'getDataBarangKeluar']);
        Route::resource('/barang-keluar', BarangKeluarController::class);

        Route::get('/stok-opname', [StokOpnameController::class, 'index']);
        Route::post('/stok-opname', [StokOpnameController::class, 'store']);
        Route::get('/stok-opname/get-data', [StokOpnameController::class, 'getDataStok']);

        Route::get('/stok-adjustment', [StokOpnameController::class, 'stokAdjustment']);
        Route::post('/stok-adjustment', [StokOpnameController::class, 'adjust']);
        Route::get('/stok-adjustment/get-data', [StokOpnameController::class, 'getDataStokOpname']);

        Route::get('/verifikasi', [VerifikasiController::class, 'index']);
        Route::post('/verifikasi-barang-masuk', [BarangMasukController::class, 'approveAll']);
        Route::post('/verifikasi-barang-keluar', [BarangKeluarController::class, 'approveAll']);
        Route::post('/verifikasi-stok-opname', [StokOpnameController::class, 'approveAll']);
    });

    Route::group(['middleware' => 'checkRole:superadmin'], function(){
        Route::get('/data-pengguna/get-data', [ManajemenUserController::class, 'getDataPengguna']);
        Route::get('/api/role/', [ManajemenUserController::class, 'getRole']);
        Route::resource('/data-pengguna', ManajemenUserController::class);
    
        Route::get('/hak-akses/get-data', [HakAksesController::class, 'getDataRole']);
        Route::resource('/hak-akses', HakAksesController::class);
        
        Route::get('/database', [DatabaseController::class, 'index'])->name('database.index');
        Route::post('/database/backup', [DatabaseController::class, 'backup'])->name('database.backup');
        Route::post('/database/restore', [DatabaseController::class, 'restore'])->name('database.restore');
        Route::delete('/database/delete', [DatabaseController::class, 'delete'])->name('database.delete');
    });

});

require __DIR__.'/auth.php';
