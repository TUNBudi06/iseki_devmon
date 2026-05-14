const DB_NAME = 'attendance-db';
const STORE_NAME = 'auth';
const KEY = 'device';

interface DeviceAuth {
    device_id: string | number;
    jwt: string;
}

/**
 * Opens the IndexedDB database for attendance.
 * Caches the DB connection to avoid reopening.
 */
let dbPromise: Promise<IDBDatabase> | null = null;

function openDB(): Promise<IDBDatabase> {
    if (dbPromise) return dbPromise;

    dbPromise = new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, 1);

        request.onupgradeneeded = (event: IDBVersionChangeEvent) => {
            const db = (event.target as IDBOpenDBRequest).result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME);
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => {
            dbPromise = null;
            reject(request.error);
        };
        request.onblocked = () => {
            console.warn('IndexedDB upgrade blocked - close other tabs');
        };
    });

    return dbPromise;
}

/**
 * Wraps an IDBRequest in a Promise and handles transaction completion.
 */
function promisifyRequest<T>(request: IDBRequest<T>): Promise<T> {
    return new Promise((resolve, reject) => {
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Wraps a transaction to ensure it completes successfully.
 */
function promisifyTransaction(tx: IDBTransaction): Promise<void> {
    return new Promise((resolve, reject) => {
        tx.oncomplete = () => resolve();
        tx.onerror = () => reject(tx.error);
        tx.onabort = () => reject(new Error('Transaction aborted'));
    });
}

/**
 * Saves device authentication data to IndexedDB.
 */
export async function saveDeviceAuth(
    device_id: string | number,
    jwt: string,
): Promise<void> {
    const db = await openDB();
    const tx = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.objectStore(STORE_NAME);

    const data: DeviceAuth = { device_id, jwt };

    store.put(data, KEY);

    // Wait for transaction to fully commit
    await promisifyTransaction(tx);
}

/**
 * Retrieves device authentication data from IndexedDB.
 */
export async function getDeviceAuth(): Promise<DeviceAuth | null> {
    const db = await openDB();
    const tx = db.transaction(STORE_NAME, 'readonly');
    const store = tx.objectStore(STORE_NAME);

    const result = await promisifyRequest<DeviceAuth | undefined>(
        store.get(KEY),
    );
    return result ?? null;
}

/**
 * Clears device authentication data from IndexedDB.
 */
export async function clearDeviceAuth(): Promise<void> {
    const db = await openDB();
    const tx = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.objectStore(STORE_NAME);

    store.delete(KEY);

    await promisifyTransaction(tx);
}

/**
 * Closes the database connection (useful for cleanup/testing).
 */
export function closeDB(): void {
    if (dbPromise) {
        dbPromise.then((db) => db.close()).catch(() => {});
        dbPromise = null;
    }
}
