import './bootstrap';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Initialize Echo
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT,
    wssPort: import.meta.env.VITE_REVERB_PORT,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// Listen for notifications if we're on user page
if (window.location.pathname.includes('/user/')) {
    const userId = window.location.pathname.split('/').pop();
    
    window.Echo.channel(`user.${userId}`)
        .listen('.notification.received', (e) => {
            console.log('New notification received:', e);
            
            // Add log
            if (window.addLog) {
                window.addLog(`New ${e.notification.type} notification: ${e.notification.message}`);
            }
            
            // Update UI based on notification type
            if (e.notification.type === 'notification_read') {
                // Reload notifications when one is marked as read
                if (window.loadNotifications) {
                    window.loadNotifications();
                }
            } else {
                // Show notification badge
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    badge.classList.remove('hidden');
                    const currentCount = parseInt(badge.textContent) || 0;
                    badge.textContent = currentCount + 1;
                }
                
                // Show browser notification
                if (Notification.permission === 'granted') {
                    new Notification('New Notification', {
                        body: e.notification.message,
                        icon: '/favicon.ico'
                    });
                }
                
                // Reload notifications
                if (window.loadNotifications) {
                    setTimeout(() => window.loadNotifications(), 1000);
                }
            }
        });
    
    // Request notification permission
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
}