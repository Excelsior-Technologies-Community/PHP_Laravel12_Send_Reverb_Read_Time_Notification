# PHP_Laravel12_Send_Reverb_Read_Time_Notification

A complete real-time notification system built with Laravel 12 using Reverb for WebSocket communication. This project demonstrates how to implement instant notifications with read and unread status tracking, user-specific channels, and live notification counters.

## Project Overview

This application shows how real-time communication can be implemented in Laravel using Reverb instead of third-party services. Notifications are delivered instantly to connected users, stored in the database, and synchronized across multiple browser sessions.

The project is suitable for learning real-time systems, interviews, and production-ready notification features.

## Features

* Real-time notifications using WebSockets
* Read and unread notification tracking
* Instant notification counter updates
* User-specific private channels
* Admin panel for sending notifications
* User dashboard for viewing and managing notifications
* Browser notification support
* RESTful API for notification management

## Technology Stack

Backend:

* PHP 8.2 or higher
* Laravel 12
* Laravel Reverb (WebSocket server)
* MySQL (or any Laravel-supported database)

Frontend:

* Blade templates
* Laravel Echo
* Pusher JS (used by Reverb)
* Axios
* Tailwind CSS (via Vite)

## Prerequisites

Before starting, make sure the following are installed:

* PHP 8.2 or higher
* Composer
* Node.js and npm
* MySQL or compatible database

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/laravel12-realtime-notifications-reverb.git
cd laravel12-realtime-notifications-reverb
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install JavaScript Dependencies

```bash
npm install
```

### Step 4: Configure Environment

Copy the example environment file and generate the application key:

```bash
cp .env.example .env
php artisan key:generate
```

Update the `.env` file with database and Reverb configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=realtime_notifications
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_CONNECTION=reverb

REVERB_APP_ID=my-app-id
REVERB_APP_KEY=my-app-key
REVERB_APP_SECRET=my-app-secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Step 5: Run Database Migrations

```bash
php artisan migrate
```

### Step 6: Seed the Database

```bash
php artisan db:seed --class=UserSeeder
```

This creates test users:

* Admin User: [admin@example.com](mailto:admin@example.com) / password
* John Doe: [john@example.com](mailto:john@example.com) / password
* Jane Smith: [jane@example.com](mailto:jane@example.com) / password

### Step 7: Build Frontend Assets

```bash
npm run build
```

## Running the Application

### Start Laravel Server

```bash
php artisan serve
```

Application runs at:

[http://localhost:8000](http://localhost:8000)

### Start Reverb WebSocket Server

Open a second terminal:

```bash
php artisan reverb:start
```

## Application Pages

Home Page:

* URL: /
* Overview and navigation links

Admin Panel:

* URL: /admin
* Send notifications to users
* View users list

User Dashboard:

* URL: /user/{id}
* View personal notifications
* Mark notifications as read
* Live unread counter

## API Endpoints

Send Notification:
POST /api/send-notification

Mark Notification as Read:
POST /api/mark-as-read/{id}

Get User Notifications:
GET /api/user-notifications/{userId}

Get Unread Notification Count:
GET /api/unread-count/{userId}

## Screenshot
### Real-time Notification System
<img width="993" height="590" alt="image" src="https://github.com/user-attachments/assets/b8436245-8018-49b1-b471-cbb4f4e0592d" />

### Admin Panel
<img width="1802" height="557" alt="image" src="https://github.com/user-attachments/assets/1ee03af6-6090-4fe1-a368-434eb71da321" />

### User Dashboard
<img width="1820" height="640" alt="image" src="https://github.com/user-attachments/assets/ad00c2bd-8a92-4334-8e6c-c62175cdf742" />


## Database Schema

Users Table:

* id
* name
* email
* password
* created_at
* updated_at

Notifications Table:

* id
* type
* notifiable_type
* notifiable_id
* data (JSON)
* read_at
* created_at
* updated_at

## How the System Works

When an admin sends a notification, it is stored in the database and broadcast to a user-specific WebSocket channel. Users subscribed to that channel receive the notification instantly without refreshing the page.

Marking a notification as read updates the database and broadcasts the change so all open sessions reflect the update immediately.

## Key Files

* app/Events/NotificationEvent.php
* app/Http/Controllers/NotificationController.php
* app/Http/Controllers/PageController.php
* app/Models/User.php
* app/Models/Notification.php
* resources/views/welcome.blade.php
* resources/views/admin.blade.php
* resources/views/user.blade.php
* resources/js/app.js

## Security

* CSRF protection enabled
* User-specific private channels
* Input validation on all endpoints
* Secure WebSocket communication

## Troubleshooting

WebSocket not connecting:

* Ensure Reverb server is running
* Check port 8080 availability
* Verify Reverb environment variables

Notifications not appearing:

* Check browser console
* Verify Echo configuration
* Ensure correct user channel subscription

Assets not loading:

* Run npm run build
* Clear cache using php artisan cache:clear

## Deployment Notes

For production:

* Use secure WebSocket (wss)
* Configure SSL certificates
* Update environment variables
* Run npm run build
* Cache config and routes

## Future Enhancements

* Authentication-based dashboards
* Email notifications
* Mobile push notifications
* Pagination for notifications
* Notification categories and filters

## License

This project is open-source and available under the MIT License.
