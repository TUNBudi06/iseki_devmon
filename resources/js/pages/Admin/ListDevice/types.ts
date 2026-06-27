// ─── Types ───────────────────────────────────────────────────
export type Brand = {
    id: string;
    name: string;
    created_at: string;
    updated_at: string;
};

export type PhoneList = {
    id: number;
    brand_id: string;
    model_id: string;
    model_name: string;
    model_type: string;
    buy_date: string;
    price: string;
    ram: string;
    storage: string;
    thumbnail: string | null;
    list_photos: string[] | null;
    registered: boolean;
    approved: boolean;
    hash_token: string | null;
    imei: string | null;
    mac_address: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    brand: Brand | null;
    departemen: string;
};

export type DepartemenOption = {
    id: string;
    name: string;
    color: string;
};
