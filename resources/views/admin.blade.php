<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Admin Panel</h1>
            <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800">← Back to Home</a>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Send Notification Form -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Send Notification</h2>
                
                <form id="sendNotificationForm">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Select User</label>
                            <select id="userId" name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">Message</label>
                            <input type="text" id="message" name="message" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2"
                                   placeholder="Enter notification message" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium mb-1">Type</label>
                            <select id="type" name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="info">Info</option>
                                <option value="success">Success</option>
                                <option value="warning">Warning</option>
                                <option value="error">Error</option>
                            </select>
                        </div>
                        
                        <button type="submit" 
                                class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition">
                            Send Notification
                        </button>
                    </div>
                </form>
                
                <div id="responseMessage" class="mt-4"></div>
            </div>
            
            <!-- Users List -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Users</h2>
                <div class="space-y-3">
                    @foreach($users as $user)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                            <div class="font-medium">{{ $user->name }}</div>
                            <div class="text-sm text-gray-600">ID: {{ $user->id }}</div>
                            <a href="{{ route('user', ['id' => $user->id]) }}" 
                               class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block">
                               View Dashboard →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('sendNotificationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                user_id: document.getElementById('userId').value,
                message: document.getElementById('message').value,
                type: document.getElementById('type').value
            };
            
            try {
                const response = await fetch('/api/send-notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                const responseDiv = document.getElementById('responseMessage');
                
                if (result.success) {
                    responseDiv.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            ${result.message}
                        </div>
                    `;
                    document.getElementById('message').value = '';
                } else {
                    responseDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            Error: ${result.message}
                        </div>
                    `;
                }
                
                // Clear message after 3 seconds
                setTimeout(() => {
                    responseDiv.innerHTML = '';
                }, 3000);
                
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
</body>
</html>