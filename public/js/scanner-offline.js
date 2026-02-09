/**
 * Scanner Offline Support
 * Handles offline mode with localStorage caching and background sync
 */

class ScannerOffline {
    constructor() {
        this.isOnline = navigator.onLine;
        this.syncQueue = [];
        this.init();
    }

    init() {
        // Listen for online/offline events
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());

        // Load sync queue from localStorage
        this.loadSyncQueue();

        // Update UI
        this.updateOnlineStatus();

        // Try to sync on load if online
        if (this.isOnline) {
            this.syncPendingScans();
        }
    }

    handleOnline() {
        this.isOnline = true;
        this.updateOnlineStatus();
        this.syncPendingScans();
        this.showNotification('Connection restored. Syncing data...', 'success');
    }

    handleOffline() {
        this.isOnline = false;
        this.updateOnlineStatus();
        this.showNotification('You are offline. Scans will be queued for sync.', 'warning');
    }

    updateOnlineStatus() {
        const indicator = document.getElementById('onlineIndicator');
        if (indicator) {
            if (this.isOnline) {
                indicator.className = 'flex items-center text-green-600';
                indicator.innerHTML = '<span class="w-2 h-2 bg-green-600 rounded-full mr-2"></span>Online';
            } else {
                indicator.className = 'flex items-center text-red-600';
                indicator.innerHTML = '<span class="w-2 h-2 bg-red-600 rounded-full mr-2"></span>Offline';
            }
        }
    }

    // Cache event data for offline use
    cacheEventData(eventId, eventData) {
        try {
            localStorage.setItem(`event_${eventId}`, JSON.stringify(eventData));
            localStorage.setItem(`event_${eventId}_timestamp`, Date.now().toString());
        } catch (e) {
            console.error('Failed to cache event data:', e);
        }
    }

    // Get cached event data
    getCachedEventData(eventId) {
        try {
            const data = localStorage.getItem(`event_${eventId}`);
            return data ? JSON.parse(data) : null;
        } catch (e) {
            console.error('Failed to get cached event data:', e);
            return null;
        }
    }

    // Queue a scan for later sync
    queueScan(scanData) {
        const queueItem = {
            id: Date.now(),
            timestamp: new Date().toISOString(),
            data: scanData
        };

        this.syncQueue.push(queueItem);
        this.saveSyncQueue();

        this.showNotification('Scan queued for sync when online', 'info');
    }

    // Save sync queue to localStorage
    saveSyncQueue() {
        try {
            localStorage.setItem('scanner_sync_queue', JSON.stringify(this.syncQueue));
        } catch (e) {
            console.error('Failed to save sync queue:', e);
        }
    }

    // Load sync queue from localStorage
    loadSyncQueue() {
        try {
            const queue = localStorage.getItem('scanner_sync_queue');
            this.syncQueue = queue ? JSON.parse(queue) : [];
        } catch (e) {
            console.error('Failed to load sync queue:', e);
            this.syncQueue = [];
        }
    }

    // Sync pending scans
    async syncPendingScans() {
        if (!this.isOnline || this.syncQueue.length === 0) {
            return;
        }

        this.showNotification(`Syncing ${this.syncQueue.length} pending scans...`, 'info');

        const failedItems = [];

        for (const item of this.syncQueue) {
            try {
                await this.syncSingleScan(item);
            } catch (e) {
                console.error('Failed to sync scan:', e);
                failedItems.push(item);
            }
        }

        // Update queue with only failed items
        this.syncQueue = failedItems;
        this.saveSyncQueue();

        if (failedItems.length === 0) {
            this.showNotification('All scans synced successfully!', 'success');
        } else {
            this.showNotification(`${failedItems.length} scans failed to sync`, 'error');
        }
    }

    // Sync a single scan
    async syncSingleScan(item) {
        const response = await fetch(item.data.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(item.data.payload)
        });

        if (!response.ok) {
            throw new Error('Sync failed');
        }

        return response.json();
    }

    // Show notification
    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            type === 'warning' ? 'bg-yellow-500' :
            'bg-blue-500'
        } text-white`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Clear all cached data
    clearCache() {
        const keys = Object.keys(localStorage);
        keys.forEach(key => {
            if (key.startsWith('event_') || key === 'scanner_sync_queue') {
                localStorage.removeItem(key);
            }
        });
        this.syncQueue = [];
        this.showNotification('Cache cleared', 'success');
    }
}

// Initialize offline support
const scannerOffline = new ScannerOffline();

// Export for use in other scripts
window.scannerOffline = scannerOffline;
