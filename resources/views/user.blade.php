<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="container mx-auto px-4 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">

            <div>
                <h1 class="text-3xl font-bold">
                    User Dashboard
                </h1>

                <p class="text-gray-600">
                    {{ $user->name }}
                    (ID : {{ $user->id }})
                </p>
            </div>

            <a href="{{ route('home') }}"
                class="text-blue-600 hover:text-blue-800">

                ← Back Home

            </a>

        </div>


        <!-- Statistics -->

        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">

            <div class="bg-white shadow rounded-lg p-5">

                <p class="text-gray-500 text-sm">
                    Total Notifications
                </p>

                <h2 id="statTotal"
                    class="text-3xl font-bold text-blue-600">
                    0
                </h2>

            </div>


            <div class="bg-white shadow rounded-lg p-5">

                <p class="text-gray-500 text-sm">
                    Read
                </p>

                <h2 id="statRead"
                    class="text-3xl font-bold text-green-600">
                    0
                </h2>

            </div>


            <div class="bg-white shadow rounded-lg p-5">

                <p class="text-gray-500 text-sm">
                    Unread
                </p>

                <h2 id="statUnread"
                    class="text-3xl font-bold text-red-600">
                    0
                </h2>

            </div>


            <div class="bg-white shadow rounded-lg p-5">

                <p class="text-gray-500 text-sm">
                    Today
                </p>

                <h2 id="statToday"
                    class="text-3xl font-bold text-purple-600">
                    0
                </h2>

            </div>

        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2">

                <!-- Notification Panel -->

                <div class="bg-white rounded-lg shadow-md p-6">

                    <div class="flex justify-between items-center mb-4">

                        <h2 class="text-xl font-semibold">
                            Notifications
                        </h2>

                        <div id="notificationBadge"
                            class="hidden bg-red-600 text-white w-7 h-7 rounded-full flex items-center justify-center">

                            0

                        </div>

                    </div>


                    <!-- Search -->

                    <div class="flex gap-3 mb-5">

                        <input
                            type="text"
                            id="searchNotification"
                            placeholder="Search notification..."
                            class="flex-1 border rounded-lg px-4 py-2">

                        <button
                            id="searchBtn"
                            class="bg-blue-600 text-white px-5 rounded-lg hover:bg-blue-700">

                            Search

                        </button>

                        <button
                            id="clearBtn"
                            class="bg-gray-500 text-white px-5 rounded-lg hover:bg-gray-600">

                            Clear

                        </button>

                    </div>


                    <!-- Notification List -->

                    <div
                        id="notificationsList"
                        class="space-y-3 max-h-[500px] overflow-y-auto">

                    </div>


                    <!-- Pagination -->

                    <div
                        id="pagination"
                        class="flex justify-center gap-2 mt-6">

                    </div>

                </div>


                <!-- Real Time Logs -->

                <div class="bg-white rounded-lg shadow-md p-6 mt-6">

                    <h2 class="text-xl font-semibold mb-4">

                        Real Time Logs

                    </h2>

                    <div
                        id="realtimeLogs"
                        class="space-y-2 max-h-52 overflow-y-auto">

                    </div>

                </div>

            </div>


            <!-- Right Side -->

            <div class="space-y-6">

                <div class="bg-white rounded-lg shadow-md p-6">

                    <h2 class="text-xl font-semibold mb-4">

                        User Information

                    </h2>

                    <div class="space-y-2">

                        <p>
                            <strong>Name :</strong>
                            {{ $user->name }}
                        </p>

                        <p>
                            <strong>User ID :</strong>
                            {{ $user->id }}
                        </p>

                        <p>
                            <strong>Total :</strong>
                            <span id="totalNotifications">
                                0
                            </span>
                        </p>

                        <p>
                            <strong>Unread :</strong>
                            <span id="unreadCount">
                                0
                            </span>
                        </p>

                    </div>

                </div>


                <div class="bg-white rounded-lg shadow-md p-6">

                    <h2 class="text-xl font-semibold mb-4">

                        Actions

                    </h2>

                    <button
                        id="refreshNotifications"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 mb-3">

                        Refresh

                    </button>

                    <button
                        id="markAllRead"
                        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">

                        Mark All Read

                    </button>

                </div>

            </div>

        </div>

    </div>

    <script>
        const userId = {
            {
                $user - > id
            }
        };

        let currentPage = 1;

        let search = "";

        // ===============================
        // Load Dashboard Statistics
        // ===============================
        async function loadStatistics() {

            try {

                const response = await fetch(`/api/statistics/${userId}`);
                const result = await response.json();

                if (result.success) {

                    document.getElementById("statTotal").innerText =
                        result.statistics.total;

                    document.getElementById("statRead").innerText =
                        result.statistics.read;

                    document.getElementById("statUnread").innerText =
                        result.statistics.unread;

                    document.getElementById("statToday").innerText =
                        result.statistics.today;

                    document.getElementById("totalNotifications").innerText =
                        result.statistics.total;

                    document.getElementById("unreadCount").innerText =
                        result.statistics.unread;

                    const badge = document.getElementById("notificationBadge");

                    if (result.statistics.unread > 0) {

                        badge.classList.remove("hidden");
                        badge.innerText = result.statistics.unread;

                    } else {

                        badge.classList.add("hidden");

                    }

                }

            } catch (error) {

                console.log(error);

            }

        }


        // ===============================
        // Load Notifications
        // ===============================
        async function loadNotifications(page = 1) {


            currentPage = page;

            try {

                const response = await fetch(
                    `/api/user-notifications/${userId}?page=${page}&search=${encodeURIComponent(search)}`
                );

                const result = await response.json();

                if (result.success) {

                    displayNotifications(result.notifications);

                    createPagination(result.pagination);

                    loadStatistics();

                }

            } catch (error) {

                console.log(error);

            }

        }



        // ===============================
        // Display Notifications
        // ===============================
        function displayNotifications(notifications) {

            console.log("Notifications received:", notifications);

            const container =
                document.getElementById("notificationsList");

            container.innerHTML = "";

            if (notifications.length === 0) {

                container.innerHTML = `
            <div class="text-center text-gray-500 py-10">
                No Notifications Found.
            </div>
        `;

                return;

            }

            notifications.forEach(notification => {

                let color = "bg-blue-100 text-blue-700";

                if (notification.type === "success") {

                    color = "bg-green-100 text-green-700";

                }

                if (notification.type === "warning") {

                    color = "bg-yellow-100 text-yellow-700";

                }

                if (notification.type === "error") {

                    color = "bg-red-100 text-red-700";

                }

                container.innerHTML += `

        <div class="border rounded-lg p-4
            ${notification.read ? "bg-gray-50" : "bg-yellow-50"}">

            <div class="flex justify-between">

                <div>

                    <span class="px-2 py-1 rounded text-xs ${color}">
                        ${notification.type}
                    </span>

                    ${
                        !notification.read
                        ?
                        `<span class="bg-red-600 text-white text-xs px-2 py-1 rounded ml-2">
                            NEW
                        </span>`
                        :
                        ""
                    }

                    <p class="mt-3 font-semibold">

                        ${notification.message}

                    </p>

                    <p class="text-sm text-gray-500 mt-1">

                        ${notification.sent_at}

                    </p>

                </div>

                <div class="flex gap-2">

                    ${
                        !notification.read
                        ?
                        `
                        <button
                            onclick="markAsRead(${notification.id})"
                            class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">

                            Read

                        </button>
                        `
                        :
                        `
                        <span class="text-green-600 text-sm">

                            Read ${notification.read_at}

                        </span>
                        `
                    }

                    <button
                        onclick="deleteNotification(${notification.id})"
                        class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">

                        Delete

                    </button>

                </div>

            </div>

        </div>

        `;

            });

        }

        // ===============================
        // Create Pagination
        // ===============================
        function createPagination(pagination) {

            const container = document.getElementById("pagination");

            container.innerHTML = "";

            if (pagination.last_page <= 1) {
                return;
            }

            // Previous Button
            if (pagination.current_page > 1) {

                container.innerHTML += `
            <button
                onclick="loadNotifications(${pagination.current_page - 1})"
                class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">
                Previous
            </button>
        `;
            }

            // Page Numbers
            for (let i = 1; i <= pagination.last_page; i++) {

                container.innerHTML += `
            <button
                onclick="loadNotifications(${i})"
                class="px-3 py-2 rounded
                ${i === pagination.current_page
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-200 hover:bg-gray-300'}">

                ${i}

            </button>
        `;
            }

            // Next Button
            if (pagination.current_page < pagination.last_page) {

                container.innerHTML += `
            <button
                onclick="loadNotifications(${pagination.current_page + 1})"
                class="px-3 py-2 bg-gray-200 rounded hover:bg-gray-300">

                Next

            </button>
        `;
            }

        }



        // ===============================
        // Mark Notification As Read
        // ===============================
        async function markAsRead(id) {

            try {

                const response = await fetch(`/api/mark-as-read/${id}`, {

                    method: "POST",

                    headers: {

                        "Content-Type": "application/json",

                        "X-CSRF-TOKEN": "{{ csrf_token() }}"

                    }

                });

                const result = await response.json();

                if (result.success) {

                    addLog("Notification marked as read.");

                    loadNotifications(currentPage);

                }

            } catch (error) {

                console.log(error);

            }

        }



        // ===============================
        // Delete Notification
        // ===============================
        async function deleteNotification(id) {

            if (!confirm("Delete this notification?")) {

                return;

            }

            try {

                const response = await fetch(`/api/delete-notification/${id}`, {

                    method: "DELETE",

                    headers: {

                        "Content-Type": "application/json",

                        "X-CSRF-TOKEN": "{{ csrf_token() }}"

                    }

                });

                const result = await response.json();

                if (result.success) {

                    addLog("Notification deleted.");

                    loadNotifications(currentPage);

                }

            } catch (error) {

                console.log(error);

            }

        }



        // ===============================
        // Search Notification
        // ===============================
        document
            .getElementById("searchBtn")
            .addEventListener("click", function() {

                search =
                    document.getElementById("searchNotification").value;

                loadNotifications(1);

            });



        // ===============================
        // Clear Search
        // ===============================
        document
            .getElementById("clearBtn")
            .addEventListener("click", function() {

                search = "";

                document.getElementById("searchNotification").value = "";

                loadNotifications(1);

            });



        // Search on Enter Key
        document
            .getElementById("searchNotification")
            .addEventListener("keyup", function(e) {

                if (e.key === "Enter") {

                    search = this.value;

                    loadNotifications(1);

                }

            }); // ===============================
        // Add Real-time Logs
        // ===============================
        function addLog(message) {

            const logsContainer =
                document.getElementById("realtimeLogs");


            const log = document.createElement("div");

            log.className =
                "bg-gray-50 rounded p-2 text-sm";


            log.innerText =
                `[${new Date().toLocaleTimeString()}] ${message}`;


            logsContainer.prepend(log);


            // Keep only latest 10 logs

            if (logsContainer.children.length > 10) {

                logsContainer.removeChild(
                    logsContainer.lastChild
                );

            }

        }



        // ===============================
        // Mark All Notifications Read
        // ===============================
        document
            .getElementById("markAllRead")
            .addEventListener("click", async function() {


                try {


                    const response = await fetch(
                        `/api/user-notifications/${userId}`
                    );


                    const result = await response.json();



                    if (result.success) {


                        const unread =
                            result.notifications.filter(
                                notification => !notification.read
                            );



                        for (const notification of unread) {


                            await fetch(
                                `/api/mark-as-read/${notification.id}`, {

                                    method: "POST",

                                    headers: {

                                        "Content-Type": "application/json",


                                        "X-CSRF-TOKEN": "{{ csrf_token() }}"

                                    }

                                }
                            );


                        }


                        addLog(
                            "All notifications marked as read."
                        );


                        loadNotifications(currentPage);


                    }



                } catch (error) {


                    console.log(error);


                }


            });




        // ===============================
        // Refresh Button
        // ===============================
        document
            .getElementById("refreshNotifications")
            .addEventListener("click", function() {


                loadNotifications(currentPage);

                loadStatistics();
                loadNotifications(currentPage);


                addLog(
                    "Notifications refreshed."
                );


            });




        // ===============================
        // Laravel Reverb Real-time Listener
        // ===============================

        if (window.Echo)


            Echo.channel(`user.${userId}`)
            .listen('.notification.received', (e) => {

                addLog("New notification received.");

                loadStatistics();

                loadNotifications(1);

            });



        // ===============================
        // Initial Page Load
        // ===============================

        loadStatistics();

        loadNotifications(1);
    </script>

</body>

</html>