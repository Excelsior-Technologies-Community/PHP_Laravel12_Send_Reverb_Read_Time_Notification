<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

// Web routes
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/admin', [PageController::class, 'admin'])->name('admin');
Route::get('/user/{id}', [PageController::class, 'user'])->name('user');

// API routes for notifications
Route::prefix('api')->group(function () {
    Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
    Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead']);
    Route::get('/user-notifications/{userId}', [NotificationController::class, 'getUserNotifications']);
    Route::get('/unread-count/{userId}', [NotificationController::class, 'getUnreadCount']);
});