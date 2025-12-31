<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold">User Dashboard</h1>
                <p class="text-gray-600">{{ $user->name }} (ID: {{ $user->id }})</p>
            </div>
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">← Back to Home</a>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Notifications Panel -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Notifications</h2>
                        <div id="notificationBadge" 
                             class="bg-red-600 text-white text-sm font-bold rounded-full w-6 h-6 flex items-center justify-center hidden">
                            0
                        </div>
                    </div>
                    
                    <div id="notificationsList" class="space-y-3 max-h-96 overflow-y-auto">
                        <!-- Notifications will be loaded here -->
                    </div>
                    
                    <button id="loadNotifications" 
                            class="mt-4 w-full bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                        Load All Notifications
                    </button>
                </div>
                
                <!-- Real-time Logs -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Real-time Logs</h2>
                    <div id="realtimeLogs" class="space-y-2 max-h-48 overflow-y-auto">
                        <!-- Real-time logs will appear here -->
                    </div>
                </div>
            </div>
            
            <!-- User Info & Stats -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">User Information</h2>
                    <div class="space-y-2">
                        <div>
                            <span class="font-medium">Name:</span>
                            <span>{{ $user->name }}</span>
                        </div>
                        <div>
                            <span class="font-medium">User ID:</span>
                            <span>{{ $user->id }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Notifications:</span>
                            <span id="totalNotifications">0</span>
                        </div>
                        <div>
                            <span class="font-medium">Unread:</span>
                            <span id="unreadCount">0</span>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Actions</h2>
                    <button id="markAllRead" 
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition mb-3">
                        Mark All as Read
                    </button>
                    <button id="refreshNotifications" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        const userId = {{ $user->id }};
        
        // Echo setup will be done in app.js
        
        // Load notifications
        async function loadNotifications() {
            try {
                const response = await fetch(`/api/user-notifications/${userId}`);
                const result = await response.json();
                
                if (result.success) {
                    displayNotifications(result.notifications);
                    updateStats(result.notifications);
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
            }
        }
        
        // Display notifications
        function displayNotifications(notifications) {
            const container = document.getElementById('notificationsList');
            const totalSpan = document.getElementById('totalNotifications');
            
            if (notifications.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">No notifications yet.</p>';
                totalSpan.textContent = '0';
                return;
            }
            
            container.innerHTML = '';
            totalSpan.textContent = notifications.length;
            
            notifications.forEach(notification => {
                const notificationElement = document.createElement('div');
                notificationElement.className = `border border-gray-200 rounded-lg p-3 ${notification.read ? 'bg-gray-50' : 'bg-yellow-50'}`;
                notificationElement.innerHTML = `
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-1 text-xs rounded-full ${
                                    notification.type === 'success' ? 'bg-green-100 text-green-800' :
                                    notification.type === 'warning' ? 'bg-yellow-100 text-yellow-800' :
                                    notification.type === 'error' ? 'bg-red-100 text-red-800' :
                                    'bg-blue-100 text-blue-800'
                                }">
                                    ${notification.type}
                                </span>
                                ${!notification.read ? '<span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">NEW</span>' : ''}
                            </div>
                            <p class="font-medium">${notification.message}</p>
                            <p class="text-sm text-gray-500 mt-1">${notification.sent_at}</p>
                        </div>
                        ${!notification.read ? `
                            <button onclick="markAsRead(${notification.id})" 
                                    class="text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition">
                                Mark as Read
                            </button>
                        ` : `
                            <span class="text-sm text-gray-500">Read ${notification.read_at}</span>
                        `}
                    </div>
                `;
                container.appendChild(notificationElement);
            });
        }
        
        // Update statistics
        function updateStats(notifications) {
            const unreadCount = notifications.filter(n => !n.read).length;
            document.getElementById('unreadCount').textContent = unreadCount;
            
            const badge = document.getElementById('notificationBadge');
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        }
        
        // Mark notification as read
        async function markAsRead(notificationId) {
            try {
                const response = await fetch(`/api/mark-as-read/${notificationId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const result = await response.json();
                if (result.success) {
                    loadNotifications();
                    addLog('Notification marked as read');
                }
            } catch (error) {
                console.error('Error marking as read:', error);
            }
        }
        
        // Add log to real-time logs
        function addLog(message) {
            const logsContainer = document.getElementById('realtimeLogs');
            const logEntry = document.createElement('div');
            logEntry.className = 'text-sm p-2 bg-gray-50 rounded';
            logEntry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            logsContainer.prepend(logEntry);
            
            // Keep only last 10 logs
            const logs = logsContainer.children;
            if (logs.length > 10) {
                logsContainer.removeChild(logs[logs.length - 1]);
            }
        }
        
        // Event listeners
        document.getElementById('loadNotifications').addEventListener('click', loadNotifications);
        document.getElementById('refreshNotifications').addEventListener('click', loadNotifications);
        
        document.getElementById('markAllRead').addEventListener('click', async () => {
            try {
                const response = await fetch(`/api/user-notifications/${userId}`);
                const result = await response.json();
                
                if (result.success) {
                    const unreadNotifications = result.notifications.filter(n => !n.read);
                    for (const notification of unreadNotifications) {
                        await markAsRead(notification.id);
                    }
                    addLog('All notifications marked as read');
                }
            } catch (error) {
                console.error('Error marking all as read:', error);
            }
        });
        
        // Initial load
        loadNotifications();
    </script>
</body>
</html>