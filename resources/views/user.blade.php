<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 min-h-screen text-slate-800">

    <div class="container mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex flex-wrap justify-between items-center mb-8 gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <!-- Live Presence Indicator -->
                    <span id="userLiveDot" class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white ring-2 ring-emerald-300" title="Online via Reverb"></span>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h1 class="text-2xl font-bold text-slate-800">{{ $user->name }}</h1>
                        <span id="userLiveBadge" class="text-xs bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full inline-flex items-center">
                            <span class="w-1.5 h-1.5 mr-1 rounded-full bg-emerald-500"></span> Online
                        </span>
                        <span id="dndHeaderBadge" class="hidden text-xs bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-full">
                            <i class="fa-solid fa-moon mr-1"></i> DND Active
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">{{ $user->email }} &bull; User ID: {{ $user->id }} &bull; Channel: <code class="text-indigo-600 font-mono">user.{{ $user->id }}</code></p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <span id="reverbStatusBadge" class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-semibold inline-flex items-center">
                    <span class="w-2 h-2 mr-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Connecting Reverb...
                </span>
                <a href="{{ route('admin') }}" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-2 rounded-lg font-medium transition">
                    <i class="fa-solid fa-user-shield mr-1"></i> Admin Panel
                </a>
                <a href="{{ route('home') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">
                    ← Back Home
                </a>
            </div>
        </div>

        <!-- Statistics Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Total Received</p>
                    <h2 id="statTotal" class="text-2xl font-bold text-slate-800 mt-0.5">0</h2>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-inbox"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Read</p>
                    <h2 id="statRead" class="text-2xl font-bold text-emerald-600 mt-0.5">0</h2>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Unread</p>
                    <h2 id="statUnread" class="text-2xl font-bold text-red-600 mt-0.5">0</h2>
                </div>
                <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-bell"></i>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase text-slate-400">Received Today</p>
                    <h2 id="statToday" class="text-2xl font-bold text-purple-600 mt-0.5">0</h2>
                </div>
                <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Notifications Center (Left 2 cols) -->
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-5">
                    <!-- Top Title and Batch Actions -->
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div class="flex items-center space-x-2">
                            <h2 class="text-lg font-bold text-slate-800">Notification Feed</h2>
                            <span id="notificationBadge" class="hidden bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                                0 Unread
                            </span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <button onclick="markAllNotificationsAsRead()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg font-medium transition cursor-pointer">
                                <i class="fa-solid fa-check-double mr-1 text-emerald-600"></i> Mark All as Read
                            </button>
                            <button onclick="loadNotifications(1)" class="text-xs bg-indigo-50 hover:bg-indigo-100 text-indigo-700 px-3 py-1.5 rounded-lg font-medium transition cursor-pointer">
                                <i class="fa-solid fa-rotate mr-1"></i> Refresh
                            </button>
                        </div>
                    </div>

                    <!-- Category & Status Filter Tabs -->
                    <div class="flex flex-wrap gap-1.5 border-b border-slate-200 pb-3" id="filterTabs">
                        <button onclick="setFilter('all')" data-filter="all" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 text-white transition">
                            All
                        </button>
                        <button onclick="setFilter('unread')" data-filter="unread" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                            Unread
                        </button>
                        <button onclick="setFilter('high_priority')" data-filter="high_priority" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                            <i class="fa-solid fa-triangle-exclamation text-red-500 mr-1"></i> High Priority
                        </button>
                        <button onclick="setFilter('security')" data-filter="security" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                            Security
                        </button>
                        <button onclick="setFilter('system')" data-filter="system" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                            System
                        </button>
                        <button onclick="setFilter('general')" data-filter="general" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                            General
                        </button>
                        <button onclick="setFilter('payment')" data-filter="payment" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition">
                            Payment
                        </button>
                    </div>

                    <!-- Search and Advanced Filtering Bar -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200 text-xs">
                        <!-- Search Box -->
                        <div class="sm:col-span-5 relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400"></i>
                            <input type="text" id="searchNotification" placeholder="Search message or action..."
                                class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <!-- Date Range From -->
                        <div class="sm:col-span-3">
                            <input type="date" id="filterFromDate" class="w-full px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs" title="From Date">
                        </div>

                        <!-- Date Range To -->
                        <div class="sm:col-span-2">
                            <input type="date" id="filterToDate" class="w-full px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs" title="To Date">
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="sm:col-span-2">
                            <select id="filterSort" class="w-full px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="priority">By Priority</option>
                            </select>
                        </div>
                    </div>

                    <!-- Notifications List Container -->
                    <div id="notificationsList" class="space-y-3 min-h-[220px]">
                        <div class="text-center text-slate-400 py-12">
                            <i class="fa-solid fa-spinner fa-spin text-2xl mb-2 text-indigo-500"></i>
                            <p class="text-xs">Loading notifications...</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination" class="flex flex-wrap justify-center items-center gap-1.5 pt-3 border-t border-slate-100"></div>
                </div>

                <!-- Real-Time Activity Logs -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <h3 class="text-sm font-bold text-slate-800">Live Reverb WebSocket Stream</h3>
                        </div>
                        <button onclick="clearLogs()" class="text-[11px] text-slate-400 hover:text-slate-600">Clear Logs</button>
                    </div>
                    <div id="realtimeLogs" class="space-y-1.5 max-h-40 overflow-y-auto font-mono text-[11px] text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-200">
                        <div class="text-slate-400">Listening to events on user.{{ $user->id }} and online-users presence channel...</div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Preferences, DND, & Presence -->
            <div class="space-y-6">

                <!-- 1. Notification Preferences & DND Mode Card -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Preferences &amp; DND</h2>
                            <p class="text-xs text-slate-500">Control notifications, sound, and silent hours</p>
                        </div>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                    </div>

                    <form id="preferencesForm" class="space-y-3 text-xs">
                        <!-- Email Notification Toggle -->
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-100">
                            <div>
                                <p class="font-bold text-slate-800">Email Notifications</p>
                                <p class="text-[11px] text-slate-500">Send an email alert for every notification</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="prefEmail" class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- Sound Alerts Toggle -->
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg border border-slate-100">
                            <div>
                                <p class="font-bold text-slate-800">Audio Chime Sound</p>
                                <p class="text-[11px] text-slate-500">Play a pleasant sound on real-time event</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="prefSound" class="sr-only peer" checked>
                                <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>

                        <!-- Do Not Disturb (DND) Toggle -->
                        <div class="p-2.5 bg-amber-50/60 rounded-lg border border-amber-200/60 space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-amber-900 flex items-center space-x-1">
                                        <i class="fa-solid fa-moon text-amber-600"></i>
                                        <span>Do Not Disturb (DND)</span>
                                    </p>
                                    <p class="text-[11px] text-amber-700">Mutes sound and email during scheduled hours</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="prefDnd" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-600"></div>
                                </label>
                            </div>

                            <!-- DND Schedule Hours -->
                            <div id="dndTimeSettings" class="grid grid-cols-2 gap-2 pt-2 border-t border-amber-200/50">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-amber-800 mb-1">Start Time</label>
                                    <input type="time" id="prefDndStart" value="22:00" class="w-full px-2 py-1 bg-white border border-amber-300 rounded text-xs">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase text-amber-800 mb-1">End Time</label>
                                    <input type="time" id="prefDndEnd" value="08:00" class="w-full px-2 py-1 bg-white border border-amber-300 rounded text-xs">
                                </div>
                            </div>
                        </div>

                        <button type="button" onclick="savePreferences()" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2 rounded-lg text-xs transition shadow cursor-pointer">
                            <i class="fa-solid fa-floppy-disk mr-1"></i> Save Preferences
                        </button>
                    </form>
                    <div id="prefSaveMessage" class="text-[11px]"></div>
                </div>

                <!-- 2. System Presence Information -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 space-y-3">
                    <h3 class="text-sm font-bold text-slate-800 flex items-center justify-between">
                        <span>Live Online Users</span>
                        <span id="presenceTotalCount" class="text-xs bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">1 Online</span>
                    </h3>
                    <p class="text-xs text-slate-500">Users currently active in the Reverb PresenceChannel:</p>
                    <div id="presenceUserList" class="space-y-1.5 text-xs text-slate-700">
                        <div class="flex items-center space-x-2 p-1.5 bg-slate-50 rounded">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-semibold">{{ $user->name }} (You)</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Notification Action Detail Modal (For interactive preview) -->
    <div id="actionModal" class="hidden fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4 border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="font-bold text-slate-800 flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>Notification Action Executed</span>
                </h3>
                <button onclick="closeActionModal()" class="text-slate-400 hover:text-slate-600 text-sm">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <p id="actionModalMessage" class="text-xs text-slate-600"></p>
            <div id="actionModalLinkContainer" class="p-3 bg-slate-50 rounded-lg text-xs font-mono text-indigo-700 break-all"></div>
            <div class="flex justify-end space-x-2 pt-2">
                <button onclick="closeActionModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-semibold">
                    Close
                </button>
                <a id="actionModalGoBtn" href="#" target="_blank" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold">
                    Proceed to Destination →
                </a>
            </div>
        </div>
    </div>

    <script>
        const userId = {{ $user->id }};
        let currentPage = 1;
        let currentFilter = 'all';
        let onlinePresenceUsers = new Map();
        let userPreferences = {
            email_notifications: true,
            sound_notifications: true,
            dnd_enabled: false,
            dnd_start_time: '22:00',
            dnd_end_time: '08:00',
            is_dnd_active: false
        };

        // Web Audio API Chime Synthesizer (No external MP3 files needed)
        function playChimeSound() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                
                // Note 1
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                gain1.gain.setValueAtTime(0.15, ctx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(ctx.currentTime);
                osc1.stop(ctx.currentTime + 0.4);

                // Note 2
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880, ctx.currentTime + 0.15); // A5
                gain2.gain.setValueAtTime(0.2, ctx.currentTime + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(ctx.currentTime + 0.15);
                osc2.stop(ctx.currentTime + 0.6);
            } catch (e) {
                console.log("Audio play error", e);
            }
        }

        // Add message to live stream logs
        function addLog(message) {
            const container = document.getElementById("realtimeLogs");
            const time = new Date().toLocaleTimeString();
            const logEntry = document.createElement("div");
            logEntry.innerHTML = `<span class="text-slate-400">[${time}]</span> ${escapeHtml(message)}`;
            container.prepend(logEntry);
        }

        function clearLogs() {
            document.getElementById("realtimeLogs").innerHTML = '';
        }

        // ===============================
        // Load Statistics
        // ===============================
        async function loadStatistics() {
            try {
                const response = await fetch(`/api/statistics/${userId}`);
                const result = await response.json();

                if (result.success) {
                    document.getElementById("statTotal").innerText = result.statistics.total;
                    document.getElementById("statRead").innerText = result.statistics.read;
                    document.getElementById("statUnread").innerText = result.statistics.unread;
                    document.getElementById("statToday").innerText = result.statistics.today;

                    const badge = document.getElementById("notificationBadge");
                    if (result.statistics.unread > 0) {
                        badge.classList.remove("hidden");
                        badge.innerText = `${result.statistics.unread} Unread`;
                    } else {
                        badge.classList.add("hidden");
                    }
                }
            } catch (error) {
                console.error("Failed to load statistics:", error);
            }
        }

        // ===============================
        // Load Notifications with Filtering & Sorting
        // ===============================
        async function loadNotifications(page = 1) {
            currentPage = page;
            const search = document.getElementById("searchNotification").value.trim();
            const fromDate = document.getElementById("filterFromDate").value;
            const toDate = document.getElementById("filterToDate").value;
            const sort = document.getElementById("filterSort").value;

            let url = `/api/user-notifications/${userId}?page=${page}&filter=${encodeURIComponent(currentFilter)}&sort=${encodeURIComponent(sort)}`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (fromDate) url += `&from_date=${encodeURIComponent(fromDate)}`;
            if (toDate) url += `&to_date=${encodeURIComponent(toDate)}`;

            try {
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    displayNotifications(result.notifications);
                    createPagination(result.pagination);
                    loadStatistics();
                }
            } catch (error) {
                console.error("Failed to load notifications:", error);
            }
        }

        // Filter tab switcher
        function setFilter(filterName) {
            currentFilter = filterName;
            document.querySelectorAll("#filterTabs .tab-btn").forEach(btn => {
                if (btn.dataset.filter === filterName) {
                    btn.className = "tab-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-600 text-white transition";
                } else {
                    btn.className = "tab-btn px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100 transition";
                }
            });
            loadNotifications(1);
        }

        // Render Notifications List
        function displayNotifications(notifications) {
            const container = document.getElementById("notificationsList");
            container.innerHTML = "";

            if (!notifications || notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-slate-400 py-12 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                        <i class="fa-regular fa-bell-slash text-2xl mb-2 text-slate-300"></i>
                        <p class="text-xs">No notifications found matching this filter.</p>
                    </div>
                `;
                return;
            }

            notifications.forEach(notification => {
                let badgeTypeClass = "bg-blue-100 text-blue-700";
                if (notification.type === "success") badgeTypeClass = "bg-emerald-100 text-emerald-700";
                if (notification.type === "warning") badgeTypeClass = "bg-amber-100 text-amber-700";
                if (notification.type === "error") badgeTypeClass = "bg-red-100 text-red-700";

                let priorityClass = "bg-slate-100 text-slate-600";
                if (notification.priority === "high") priorityClass = "bg-red-100 text-red-700 font-bold";
                if (notification.priority === "medium") priorityClass = "bg-amber-100 text-amber-700";
                if (notification.priority === "low") priorityClass = "bg-slate-100 text-slate-600";

                // Actionable Button if present
                let actionBtnHtml = "";
                if (notification.action_text) {
                    const actionUrl = notification.action_url || "";
                    actionBtnHtml = `
                        <button onclick="handleNotificationAction(${notification.id}, '${escapeQuote(actionUrl)}', '${escapeQuote(notification.action_text)}')"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-3 py-1 rounded-lg transition inline-flex items-center space-x-1 cursor-pointer shadow-sm">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            <span>${escapeHtml(notification.action_text)}</span>
                        </button>
                    `;
                }

                container.innerHTML += `
                    <div class="border rounded-xl p-4 transition ${notification.read ? "bg-white border-slate-200" : "bg-indigo-50/40 border-indigo-200 shadow-sm"}">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="space-y-1.5 flex-1">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold ${badgeTypeClass}">${escapeHtml(notification.type)}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase font-semibold bg-purple-100 text-purple-700">${escapeHtml(notification.category)}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] uppercase ${priorityClass}">${escapeHtml(notification.priority)}</span>
                                    ${!notification.read ? '<span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full uppercase">NEW</span>' : ''}
                                </div>
                                <p class="text-sm font-semibold text-slate-800 leading-snug">${escapeHtml(notification.message)}</p>
                                <p class="text-[11px] text-slate-400 flex items-center space-x-1">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>${escapeHtml(notification.sent_at)}</span>
                                    ${notification.read ? `<span class="text-emerald-600 font-medium ml-2">&bull; Read ${escapeHtml(notification.read_at || '')}</span>` : ''}
                                </p>
                            </div>

                            <div class="flex items-center space-x-2">
                                ${actionBtnHtml}
                                ${!notification.read ? `
                                    <button onclick="markAsRead(${notification.id})"
                                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs px-2.5 py-1 rounded-lg transition cursor-pointer" title="Mark as Read">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                ` : ''}
                                <button onclick="deleteNotification(${notification.id})"
                                    class="text-slate-400 hover:text-red-600 text-xs p-1 rounded transition cursor-pointer" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        // Action button click handler
        async function handleNotificationAction(id, actionUrl, actionText) {
            // Auto mark as read
            await markAsRead(id, false);

            if (actionUrl && actionUrl.trim() !== '') {
                // If it's a full URL or relative route, navigate or show dialog
                if (actionUrl.startsWith('/') || actionUrl.startsWith('http://') || actionUrl.startsWith('https://')) {
                    window.location.href = actionUrl;
                    return;
                }
            }

            // Otherwise show action completion modal
            document.getElementById('actionModalMessage').textContent = `Action "${actionText}" executed for notification #${id}.`;
            document.getElementById('actionModalLinkContainer').textContent = actionUrl || 'No external URL specified (Action recorded).';
            const goBtn = document.getElementById('actionModalGoBtn');
            if (actionUrl) {
                goBtn.href = actionUrl;
                goBtn.classList.remove('hidden');
            } else {
                goBtn.classList.add('hidden');
            }
            document.getElementById('actionModal').classList.remove('hidden');
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
        }

        // Create Pagination Buttons
        function createPagination(pagination) {
            const container = document.getElementById("pagination");
            container.innerHTML = "";

            if (!pagination || pagination.last_page <= 1) return;

            if (pagination.current_page > 1) {
                container.innerHTML += `
                    <button onclick="loadNotifications(${pagination.current_page - 1})"
                        class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs hover:bg-slate-200 font-medium">
                        Prev
                    </button>
                `;
            }

            for (let i = 1; i <= pagination.last_page; i++) {
                container.innerHTML += `
                    <button onclick="loadNotifications(${i})"
                        class="px-2.5 py-1 rounded-lg text-xs font-semibold ${i === pagination.current_page ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'}">
                        ${i}
                    </button>
                `;
            }

            if (pagination.current_page < pagination.last_page) {
                container.innerHTML += `
                    <button onclick="loadNotifications(${pagination.current_page + 1})"
                        class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs hover:bg-slate-200 font-medium">
                        Next
                    </button>
                `;
            }
        }

        // Mark single as read
        async function markAsRead(id, reload = true) {
            try {
                const response = await fetch(`/api/mark-as-read/${id}`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
                });
                const result = await response.json();
                if (result.success) {
                    addLog(`Notification #${id} marked as read.`);
                    if (reload) loadNotifications(currentPage);
                }
            } catch (error) {
                console.error("Mark read error:", error);
            }
        }

        // Mark all notifications as read
        async function markAllNotificationsAsRead() {
            try {
                const response = await fetch(`/api/mark-all-read/${userId}`, {
                    method: 'POST',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
                });
                const result = await response.json();
                if (result.success) {
                    addLog("All notifications marked as read.");
                    loadNotifications(1);
                }
            } catch (error) {
                console.error("Mark all read error:", error);
            }
        }

        // Delete single notification
        async function deleteNotification(id) {
            if (!confirm("Are you sure you want to delete this notification?")) return;
            try {
                const response = await fetch(`/api/delete-notification/${id}`, {
                    method: 'DELETE',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}
                });
                const result = await response.json();
                if (result.success) {
                    addLog(`Notification #${id} deleted.`);
                    loadNotifications(currentPage);
                }
            } catch (error) {
                console.error("Delete notification error:", error);
            }
        }

        // ===============================
        // Notification Preferences & DND
        // ===============================
        async function loadPreferences() {
            try {
                const res = await fetch(`/api/user-preferences/${userId}`);
                const data = await res.json();
                if (data.success) {
                    userPreferences = data.preferences;
                    document.getElementById('prefEmail').checked = userPreferences.email_notifications;
                    document.getElementById('prefSound').checked = userPreferences.sound_notifications;
                    document.getElementById('prefDnd').checked = userPreferences.dnd_enabled;
                    document.getElementById('prefDndStart').value = userPreferences.dnd_start_time || '22:00';
                    document.getElementById('prefDndEnd').value = userPreferences.dnd_end_time || '08:00';

                    const dndHeaderBadge = document.getElementById('dndHeaderBadge');
                    if (userPreferences.is_dnd_active) {
                        dndHeaderBadge.classList.remove('hidden');
                    } else {
                        dndHeaderBadge.classList.add('hidden');
                    }
                }
            } catch (e) {
                console.error("Failed to load preferences:", e);
            }
        }

        async function savePreferences() {
            const payload = {
                email_notifications: document.getElementById('prefEmail').checked,
                sound_notifications: document.getElementById('prefSound').checked,
                dnd_enabled: document.getElementById('prefDnd').checked,
                dnd_start_time: document.getElementById('prefDndStart').value,
                dnd_end_time: document.getElementById('prefDndEnd').value
            };

            const msgDiv = document.getElementById('prefSaveMessage');
            msgDiv.textContent = 'Saving...';
            msgDiv.className = 'text-[11px] text-slate-500';

            try {
                const res = await fetch(`/api/user-preferences/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    userPreferences = data.preferences;
                    msgDiv.textContent = '✓ Preferences saved!';
                    msgDiv.className = 'text-[11px] text-emerald-600 font-semibold';
                    setTimeout(() => msgDiv.textContent = '', 3000);

                    const dndHeaderBadge = document.getElementById('dndHeaderBadge');
                    if (userPreferences.is_dnd_active) {
                        dndHeaderBadge.classList.remove('hidden');
                    } else {
                        dndHeaderBadge.classList.add('hidden');
                    }
                    addLog("Notification preferences updated.");
                }
            } catch (e) {
                msgDiv.textContent = 'Failed to save preferences.';
                msgDiv.className = 'text-[11px] text-red-600';
            }
        }

        // ===============================
        // Real-Time Reverb WebSocket Listener
        // ===============================
        window.addEventListener('DOMContentLoaded', () => {
            // Load initial state
            loadPreferences();
            loadStatistics();
            loadNotifications(1);

            // Filter input change listeners
            document.getElementById("searchNotification").addEventListener("input", debounce(() => loadNotifications(1), 350));
            document.getElementById("filterFromDate").addEventListener("change", () => loadNotifications(1));
            document.getElementById("filterToDate").addEventListener("change", () => loadNotifications(1));
            document.getElementById("filterSort").addEventListener("change", () => loadNotifications(1));

            if (window.Echo) {
                const statusBadge = document.getElementById('reverbStatusBadge');

                Echo.connector.pusher.connection.bind('connected', () => {
                    statusBadge.className = "text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-semibold inline-flex items-center";
                    statusBadge.innerHTML = '<span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span> Reverb Connected';
                    addLog("Connected to Reverb WebSockets.");
                });

                Echo.connector.pusher.connection.bind('unavailable', () => {
                    statusBadge.className = "text-xs px-2.5 py-1 rounded-full bg-red-100 text-red-800 font-semibold inline-flex items-center";
                    statusBadge.innerHTML = '<span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-red-500"></span> Reverb Disconnected';
                    addLog("Reverb connection unavailable.");
                });

                // 1. Listen to Private User Channel for notifications
                Echo.channel(`user.${userId}`)
                    .listen('.notification.received', (e) => {
                        const notif = e.notification || e;
                        const isDndActive = notif.dnd_active || userPreferences.is_dnd_active;
                        const isSoundAllowed = notif.sound_enabled && !isDndActive && userPreferences.sound_notifications;

                        if (isSoundAllowed) {
                            playChimeSound();
                        }

                        if (isDndActive) {
                            addLog(`New notification #${notif.id} received quietly (Do Not Disturb active).`);
                        } else {
                            addLog(`New notification received: "${notif.message}" [${notif.category}]`);
                        }

                        loadStatistics();
                        loadNotifications(1);
                    })
                    .listen('.notification_read', (e) => {
                        addLog("A notification was marked as read.");
                        loadStatistics();
                        loadNotifications(currentPage);
                    })
                    .listen('.all_notifications_read', (e) => {
                        addLog("All notifications marked as read.");
                        loadStatistics();
                        loadNotifications(currentPage);
                    });

                // 2. Join Presence Channel for live online user tracking
                Echo.join('online-users')
                    .here((users) => {
                        onlinePresenceUsers.clear();
                        users.forEach(u => onlinePresenceUsers.set(parseInt(u.id), u));
                        renderPresenceList();
                    })
                    .joining((user) => {
                        onlinePresenceUsers.set(parseInt(user.id), user);
                        renderPresenceList();
                        addLog(`User "${user.name}" joined online presence.`);
                    })
                    .leaving((user) => {
                        onlinePresenceUsers.delete(parseInt(user.id));
                        renderPresenceList();
                        addLog(`User "${user.name}" left.`);
                    });
            }
        });

        function renderPresenceList() {
            const listEl = document.getElementById('presenceUserList');
            const totalBadge = document.getElementById('presenceTotalCount');
            totalBadge.textContent = `${onlinePresenceUsers.size} Online`;

            listEl.innerHTML = '';
            onlinePresenceUsers.forEach((user, id) => {
                const isCurrent = id === userId;
                const row = document.createElement('div');
                row.className = 'flex items-center justify-between p-1.5 bg-slate-50 rounded';
                row.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="font-semibold text-slate-800">${escapeHtml(user.name)} ${isCurrent ? '<span class="text-indigo-600 text-[10px]">(You)</span>' : ''}</span>
                    </div>
                    <span class="text-[10px] text-slate-400">ID: ${id}</span>
                `;
                listEl.appendChild(row);
            });
        }

        // Helpers
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function escapeHtml(str) {
            if (!str) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function escapeQuote(str) {
            if (!str) return '';
            return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }
    </script>
</body>

</html>