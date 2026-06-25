import storage from '$routes/storage';

export function assetUrl(path: string | null): string | null {
    if (!path) return null;
    const clean = path.replace(/^storage\//, '');
    return storage.local({ path: clean }).url;
}

export function formatPrice(val: string): string {
    const num = parseInt(val.replace(/\D/g, ''), 10);
    if (isNaN(num)) return val;
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(num);
}
