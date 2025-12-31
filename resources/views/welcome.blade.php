<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-time Notifications</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Real-time Notification System</h1>
        
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Welcome to Real-time Notifications</h2>
            <p class="mb-6">This system demonstrates real-time notifications using Laravel Reverb.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('admin') }}" 
                   class="bg-blue-600 text-white px-4 py-3 rounded-lg text-center hover:bg-blue-700 transition">
                   Go to Admin Panel
                </a>
                <a href="{{ route('user', ['id' => 1]) }}" 
                   class="bg-green-600 text-white px-4 py-3 rounded-lg text-center hover:bg-green-700 transition">
                   View User 1 Dashboard
                </a>
            </div>
            
            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold mb-2">Features:</h3>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Real-time notifications using WebSockets</li>
                    <li>Read status tracking</li>
                    <li>Notification count updates</li>
                    <li>Instant broadcast to specific users</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>