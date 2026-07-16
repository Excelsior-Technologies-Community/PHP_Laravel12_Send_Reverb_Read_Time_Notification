<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [PageController::class, 'home'])->name('home');

// Admin Panel
Route::get('/admin', [PageController::class, 'admin'])->name('admin');

// User Dashboard
Route::get('/user/{id}', [PageController::class, 'user'])->name('user');

/*
|--------------------------------------------------------------------------
| Notification API
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {

    // Send Notification
    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);

    // Mark Notification as Read
    Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead']);

    // Get Notifications (Search + Pagination)
    Route::get('/user-notifications/{userId}', [NotificationController::class, 'getUserNotifications']);

    // Delete Notification
    Route::delete('/delete-notification/{id}', [NotificationController::class, 'deleteNotification']);

    // Dashboard Statistics
    Route::get('/statistics/{userId}', [NotificationController::class, 'getStatistics']);

    // Unread Count
    Route::get('/unread-count/{userId}', [NotificationController::class, 'getUnreadCount']);
});
