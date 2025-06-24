<?php

// use App\Http\Controllers\ProfileController;
// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__.'/auth.php';





use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\CompanyController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\UserProfileController;



Route::get('/', function () {
    // return view('/login');
    return redirect()->route('login');
});
Route::get('/login', [UserLoginController::class, 'index'])->name('login');

Route::get('/admin/login', [AdminLoginController::class, 'index'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'check'])->name('admin.check');
// Route::post('/admin/check', [AdminLoginController::class, 'check'])->name('admin.check');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

Route::get('/user/login', [UserLoginController::class, 'index'])->name('user.login');
Route::post('/user/login', [UserLoginController::class, 'check'])->name('check');
Route::post('/user/logout', [UserLoginController::class, 'logout'])->name('user.logout');



Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/tenders', [TenderController::class, 'index'])->name('admin.tenders.index');
    Route::get('/admin/tenders/create', [TenderController::class, 'create'])->name('admin.tenders.create');
    Route::post('/admin/tenders', [TenderController::class, 'store'])->name('admin.tenders.store');
    Route::get('/admin/tenders/{id}/edit', [TenderController::class, 'edit'])->name('admin.tenders.edit');
    Route::post('/admin/tenders/{id}', [TenderController::class, 'update'])->name('admin.tenders.update');
    Route::post('/admin/tenders/{id}/delete', [TenderController::class, 'destroy'])->name('admin.tenders.destroy');


    Route::get('/admin/users', [AdminLoginController::class, 'listing'])->name('admin.users.listing');
    Route::get('/admin/users/create', [AdminLoginController::class, 'create'])->name('admin.users.create');
    Route::get('/admin/users/{user}/edit', [AdminLoginController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminLoginController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminLoginController::class, 'destroy'])->name('admin.users.destroy');
});


Route::middleware(['auth', 'user'])->group(function () {

    Route::post('profile/verify-otp', [UserProfileController::class, 'verifyOtp'])->name('profile.verify.otp');
Route::post('profile/resend-otp', [UserProfileController::class, 'resendOtp'])->name('profile.resend.otp');



    Route::get('/user/dashboard', [UserDashboardController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/user/tenders', [TenderController::class, 'userIndex'])->name('user.tenders.index');
    Route::get('/user/tenders/{id}', [TenderController::class, 'userShow'])->name('user.tenders.show');
    Route::get('/user/certificate/{id}', [TenderController::class, 'certificateShow'])->name('user.tenders.certificate');
    Route::get('/user/company/edit', [CompanyController::class, 'edit'])->name('user.company.edit');
    Route::post('/user/company/update', [CompanyController::class, 'update'])->name('user.company.update');
    Route::get('/user/profile', [UserProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/user/profile', [UserProfileController::class, 'update'])->name('profile.update');
    Route::put('/user/password', [UserProfileController::class, 'updatePassword'])->name('password.update');
    Route::get('/user/email/verify/{id}/{hash}', [UserProfileController::class, 'verifyEmail'])
        ->middleware(['signed'])
        ->name('verification.verify');
    Route::post('/user/email/resend', [UserProfileController::class, 'resendVerificationEmail'])
        ->name('verification.send');
    Route::post('/user/email/resend-ajax', [UserProfileController::class, 'resendVerificationAjax'])
        ->name('verification.send.ajax');
    Route::get('/user/email/verify', [UserProfileController::class, 'details'])
        ->name('verification.notice');
    Route::get('/user/profile/details', [UserProfileController::class, 'details'])->name('profile.details');
    Route::post('/user/profile/details', [UserProfileController::class, 'storeDetails'])->name('profile.details.store');
});
?>
