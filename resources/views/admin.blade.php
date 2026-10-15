<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Real-Time Reverb Notifications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 min-h-screen text-slate-800">
    <div class="container mx-auto px-4 py-8">
        <!-- Top Bar -->
        <div class="flex flex-wrap justify-between items-center mb-8 gap-4 bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-lg font-bold shadow">
                    <i class="fa-solid fa-tower-broadcast"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold leading-tight">Admin Notification Center</h1>
                    <p class="text-xs text-slate-500">Laravel 12 &amp; Reverb WebSockets Real-Time Broadcasting</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <span id="reverbConnectionStatus" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                    <span class="w-2 h-2 mr-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Connecting to Reverb...
                </span>
                <a href="{{ route('home') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                    ← Back to Home
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Send Notification Form (Left 2 cols) -->
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Send Targeted Notification</h2>
                        <p class="text-xs text-slate-500">Dispatch instant notifications with actionable buttons over Reverb</p>
                    </div>
                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2.5 py-1 rounded-md font-semibold border border-indigo-100">
                        <i class="fa-solid fa-bolt mr-1"></i> Instant Reverb Broadcast
                    </span>
                </div>

                <form id="sendNotificationForm" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Target User</label>
                        <select id="userId" name="user_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} (ID: {{ $user->id }} - {{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Notification Message</label>
                        <input type="text" id="message" name="message"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="e.g. Your monthly statement is ready for review." required>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Visual Type</label>
                            <select id="type" name="type" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                <option value="info">Info (Blue)</option>
                                <option value="success">Success (Green)</option>
                                <option value="warning">Warning (Amber)</option>
                                <option value="error">Error (Red)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Category</label>
                            <select id="category" name="category" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                <option value="general">General</option>
                                <option value="security">Security</option>
                                <option value="payment">Payment / Billing</option>
                                <option value="system">System</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Priority</label>
                            <select id="priority" name="priority" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                                <option value="low">Low Priority</option>
                                <option value="medium" selected>Medium Priority</option>
                                <option value="high">High Priority (Urgent)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Actionable Link & Button Options -->
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <div class="flex items-center space-x-2 text-xs font-bold text-slate-700 uppercase">
                            <i class="fa-solid fa-arrow-up-right-from-square text-indigo-600"></i>
                            <span>Actionable Link &amp; Button (Optional)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Button Label</label>
                                <input type="text" id="actionText" name="action_text"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="e.g. View Order, Verify Account, Open Ticket">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-600 mb-1">Target Action URL</label>
                                <input type="text" id="actionUrl" name="action_url"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="e.g. /user/1 or https://example.com/order/101">
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500">When the recipient clicks this button, the notification will automatically be marked as read and navigated to the destination.</p>
                    </div>

                    <button type="submit" id="submitBtn"
                        class="w-full bg-indigo-600 text-white font-semibold py-2.5 rounded-lg hover:bg-indigo-700 transition shadow cursor-pointer flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Broadcast Notification via Reverb</span>
                    </button>
                </form>

                <div id="responseMessage" class="mt-4"></div>
            </div>

            <!-- Users List with Live Reverb Presence Channel (Right col) -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Users &amp; Presence</h2>
                        <p class="text-xs text-slate-500">Live PresenceChannel via Reverb</p>
                    </div>
                    <span id="onlineCountBadge" class="text-xs bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">
                        0 Online
                    </span>
                </div>

                <div class="space-y-2.5" id="usersPresenceList">
                    @foreach($users as $user)
                    <div id="user-card-{{ $user->id }}" class="border border-slate-200 rounded-xl p-3 hover:bg-slate-50 transition flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <!-- Online / Offline Status Dot -->
                                <span id="status-dot-{{ $user->id }}" class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-slate-400 border-2 border-white" title="Offline"></span>
                            </div>
                            <div>
                                <div class="font-semibold text-xs text-slate-800">{{ $user->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $user->email }}</div>
                            </div>
                        </div>

                        <div class="text-right">
                            <span id="status-text-{{ $user->id }}" class="text-[11px] text-slate-400 font-medium block">
                                Offline
                            </span>
                            <a href="{{ route('user', ['id' => $user->id]) }}" target="_blank"
                                class="text-[11px] text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center mt-0.5">
                                Dashboard <i class="fa-solid fa-arrow-up-right-from-square ml-1 text-[9px]"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store online user IDs tracked via Reverb Presence Channel
        const onlineUsers = new Set();

        function updatePresenceUI() {
            const countBadge = document.getElementById('onlineCountBadge');
            countBadge.textContent = `${onlineUsers.size} Online`;

            document.querySelectorAll('[id^="status-dot-"]').forEach(dot => {
                const userId = parseInt(dot.id.replace('status-dot-', ''));
                const text = document.getElementById(`status-text-${userId}`);

                if (onlineUsers.has(userId)) {
                    dot.className = "absolute bottom-0 right-0 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white ring-2 ring-emerald-300";
                    dot.title = "Online";
                    if (text) {
                        text.textContent = "Online";
                        text.className = "text-[11px] text-emerald-600 font-bold block";
                    }
                } else {
                    dot.className = "absolute bottom-0 right-0 w-3 h-3 rounded-full bg-slate-400 border-2 border-white";
                    dot.title = "Offline";
                    if (text) {
                        text.textContent = "Offline";
                        text.className = "text-[11px] text-slate-400 font-medium block";
                    }
                }
            });
        }

        // Initialize Reverb Presence Channel Listener
        window.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                const connStatus = document.getElementById('reverbConnectionStatus');

                Echo.connector.pusher.connection.bind('connected', () => {
                    connStatus.className = "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800";
                    connStatus.innerHTML = '<span class="w-2 h-2 mr-1.5 rounded-full bg-emerald-500"></span> Reverb Connected';
                });

                Echo.connector.pusher.connection.bind('unavailable', () => {
                    connStatus.className = "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800";
                    connStatus.innerHTML = '<span class="w-2 h-2 mr-1.5 rounded-full bg-red-500"></span> Reverb Offline';
                });

                // Join the Presence Channel
                Echo.join('online-users')
                    .here((users) => {
                        onlineUsers.clear();
                        users.forEach(u => onlineUsers.add(parseInt(u.id)));
                        updatePresenceUI();
                    })
                    .joining((user) => {
                        onlineUsers.add(parseInt(user.id));
                        updatePresenceUI();
                    })
                    .leaving((user) => {
                        onlineUsers.delete(parseInt(user.id));
                        updatePresenceUI();
                    });
            }
        });

        // Form Submit Handler
        document.getElementById('sendNotificationForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i><span>Sending via Reverb...</span>';

            const formData = {
                user_id: document.getElementById('userId').value,
                message: document.getElementById('message').value,
                type: document.getElementById('type').value,
                category: document.getElementById('category').value,
                priority: document.getElementById('priority').value,
                action_text: document.getElementById('actionText').value.trim() || null,
                action_url: document.getElementById('actionUrl').value.trim() || null,
            };

            try {
                const response = await fetch('/api/send-notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();
                const responseDiv = document.getElementById('responseMessage');

                if (result.success) {
                    let dndNote = '';
                    if (result.dnd_active) {
                        dndNote = ' <span class="block text-xs font-semibold text-amber-700 mt-1"><i class="fa-solid fa-moon mr-1"></i>Recipient has Do Not Disturb (DND) active; audio/email suppressed.</span>';
                    }

                    responseDiv.innerHTML = `
                        <div class="bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-lg text-xs shadow-sm">
                            <div class="font-bold flex items-center space-x-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span>${result.message}</span>
                            </div>
                            ${dndNote}
                        </div>
                    `;
                    document.getElementById('message').value = '';
                    document.getElementById('actionText').value = '';
                    document.getElementById('actionUrl').value = '';
                } else {
                    responseDiv.innerHTML = `
                        <div class="bg-red-50 border border-red-300 text-red-800 px-4 py-3 rounded-lg text-xs shadow-sm">
                            <span class="font-bold">Error:</span> ${result.message || 'Failed to dispatch notification.'}
                        </div>
                    `;
                }

                setTimeout(() => {
                    responseDiv.innerHTML = '';
                }, 5000);

            } catch (error) {
                console.error('Error:', error);
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i><span>Broadcast Notification via Reverb</span>';
            }
        });
    </script>
</body>

</html>