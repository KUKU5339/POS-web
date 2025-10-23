// IndexedDB wrapper for offline storage
const DB_NAME = 'StreetPOS_OfflineDB';
const DB_VERSION = 1;

class OfflineDB {
    constructor() {
        this.db = null;
    }

    // Initialize the database
    async init() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(DB_NAME, DB_VERSION);

            request.onerror = () => {
                console.error('❌ Failed to open database');
                reject(request.error);
            };

            request.onsuccess = () => {
                this.db = request.result;
                console.log('✅ Database opened successfully');
                resolve(this.db);
            };

            request.onupgradeneeded = (event) => {
                const db = event.target.result;

                // Create object stores if they don't exist
                if (!db.objectStoreNames.contains('products')) {
                    const productsStore = db.createObjectStore('products', { keyPath: 'id' });
                    productsStore.createIndex('user_id', 'user_id', { unique: false });
                    console.log('📦 Created products store');
                }

                if (!db.objectStoreNames.contains('pendingSales')) {
                    db.createObjectStore('pendingSales', { keyPath: 'id', autoIncrement: true });
                    console.log('📦 Created pendingSales store');
                }

                if (!db.objectStoreNames.contains('offlineQueue')) {
                    db.createObjectStore('offlineQueue', { keyPath: 'id', autoIncrement: true });
                    console.log('📦 Created offlineQueue store');
                }
            };
        });
    }

    // Save products to IndexedDB
    async saveProducts(products) {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['products'], 'readwrite');
        const store = transaction.objectStore('products');

        // Clear existing products first
        await store.clear();

        // Add all products
        products.forEach(product => {
            store.put(product);
        });

        return new Promise((resolve, reject) => {
            transaction.oncomplete = () => {
                console.log('✅ Products saved to IndexedDB:', products.length);
                resolve();
            };
            transaction.onerror = () => {
                console.error('❌ Failed to save products');
                reject(transaction.error);
            };
        });
    }

    // Get all products from IndexedDB
    async getProducts() {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['products'], 'readonly');
        const store = transaction.objectStore('products');
        const request = store.getAll();

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                console.log('📦 Retrieved products:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('❌ Failed to get products');
                reject(request.error);
            };
        });
    }

    // Add a pending sale to IndexedDB
    async addPendingSale(saleData) {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['pendingSales'], 'readwrite');
        const store = transaction.objectStore('pendingSales');

        // Add timestamp
        saleData.timestamp = new Date().toISOString();
        saleData.synced = false;

        const request = store.add(saleData);

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                console.log('✅ Pending sale added to IndexedDB');
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('❌ Failed to add pending sale');
                reject(request.error);
            };
        });
    }

    // Get all pending sales
    async getPendingSales() {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['pendingSales'], 'readonly');
        const store = transaction.objectStore('pendingSales');
        const request = store.getAll();

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                console.log('📦 Retrieved pending sales:', request.result.length);
                resolve(request.result);
            };
            request.onerror = () => {
                console.error('❌ Failed to get pending sales');
                reject(request.error);
            };
        });
    }

    // Remove a pending sale after successful sync
    async removePendingSale(id) {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['pendingSales'], 'readwrite');
        const store = transaction.objectStore('pendingSales');
        const request = store.delete(id);

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                console.log('✅ Pending sale removed from IndexedDB');
                resolve();
            };
            request.onerror = () => {
                console.error('❌ Failed to remove pending sale');
                reject(request.error);
            };
        });
    }

    // Clear all pending sales (after successful sync)
    async clearPendingSales() {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['pendingSales'], 'readwrite');
        const store = transaction.objectStore('pendingSales');
        const request = store.clear();

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                console.log('✅ All pending sales cleared');
                resolve();
            };
            request.onerror = () => {
                console.error('❌ Failed to clear pending sales');
                reject(request.error);
            };
        });
    }

    // Update product stock locally (for offline mode)
    async updateProductStock(productId, newStock) {
        if (!this.db) await this.init();

        const transaction = this.db.transaction(['products'], 'readwrite');
        const store = transaction.objectStore('products');
        const request = store.get(productId);

        return new Promise((resolve, reject) => {
            request.onsuccess = () => {
                const product = request.result;
                if (product) {
                    product.stock = newStock;
                    const updateRequest = store.put(product);

                    updateRequest.onsuccess = () => {
                        console.log('✅ Product stock updated locally');
                        resolve(product);
                    };
                    updateRequest.onerror = () => {
                        console.error('❌ Failed to update product stock');
                        reject(updateRequest.error);
                    };
                } else {
                    reject(new Error('Product not found'));
                }
            };
            request.onerror = () => {
                console.error('❌ Failed to get product for update');
                reject(request.error);
            };
        });
    }
}

// Create a singleton instance
const offlineDB = new OfflineDB();

// Initialize on load
if (typeof window !== 'undefined') {
    window.addEventListener('load', () => {
        offlineDB.init().catch(err => {
            console.error('Failed to initialize offline database:', err);
        });
    });
}
