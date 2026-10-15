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

    // Send Notification (Supports Action Text, Action URL, and Preferences check)
    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);

    // Mark Single Notification as Read
    Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead']);

    // Mark All Notifications as Read for User
    Route::post('/mark-all-read/{userId}', [NotificationController::class, 'markAllAsRead']);

    // Get Notifications (Search + Filters + Category Tabs + Pagination + Sorting)
    Route::get('/user-notifications/{userId}', [NotificationController::class, 'getUserNotifications']);

    // Delete Notification
    Route::delete('/delete-notification/{id}', [NotificationController::class, 'deleteNotification']);

    // Dashboard Statistics
    Route::get('/statistics/{userId}', [NotificationController::class, 'getStatistics']);

    // Unread Count
    Route::get('/unread-count/{userId}', [NotificationController::class, 'getUnreadCount']);

    // User Notification Preferences & DND Status
    Route::get('/user-preferences/{userId}', [NotificationController::class, 'getPreferences']);
    Route::post('/user-preferences/{userId}', [NotificationController::class, 'updatePreferences']);
});
