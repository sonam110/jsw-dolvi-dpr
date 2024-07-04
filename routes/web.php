<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfViewController;
use App\Http\Controllers\FrontController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return 'Application working fine.';
    //return view('welcome');
});

Route::get('/optimize-command', function () {
    \Artisan::call('optimize:clear');
    \Artisan::call('cache:forget spatie.permission.cache');
    return redirect('/');
});

Route::get('/load-pdf', [PdfViewController::class, 'loadPdf']);
Route::get('/load-excel', [PdfViewController::class, 'loadExcel']);



Route::get('dashboard/{access_key}', [FrontController::class, 'dashboard'])->name('dashboard');
Route::get('dpr-report/{access_key}', [FrontController::class, 'dprReport'])->name('dpr-report');
Route::post('get-dpr-report', [FrontController::class, 'getDprReport'])->name('get-dpr-report');
Route::post('download-dpr-report', [FrontController::class, 'downloadDprReport'])->name('download-dpr-report');
Route::post('get-upload-graph', [FrontController::class, 'getUploadsGraph'])->name('get-upload-graph');
Route::post('get-manpower-graph', [FrontController::class, 'getManpowerGraph'])->name('get-manpower-graph');
Route::get('summary-report/{access_key}', [FrontController::class, 'summeryReport'])->name('summery-report');
Route::post('get-summery-report', [FrontController::class, 'getSummeryReport'])->name('get-summery-report');


Route::get('/test-mail', function () {
    if (env('IS_MAIL_ENABLE', false) == true) {
        $otpSend = rand(100000,999999);
        $content = [
            "name" => 'Testing Name',
            "body" => 'your verification otp is : '.$otpSend,
        ];
        $recevier = Mail::to('ashok@nrt.co.in')->send(new VerifyOtpMail($content));
        print_r($recevier);
    }
    print_r('out'); 

});
