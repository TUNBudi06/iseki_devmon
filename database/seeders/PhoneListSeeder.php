<?php

namespace Database\Seeders;

use App\Models\PhoneList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhoneListSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $devices = [
            // VIVO Y02 — 3 unit
            ['brand_id' => 'vivo', 'model_name' => 'Y02', 'model_type' => 'Phone', 'buy_date' => '20-Feb-26', 'price' => '979168', 'ram' => '3 GB', 'storage' => '32 GB', 'imei' => '861751069207112'],
            ['brand_id' => 'vivo', 'model_name' => 'Y02', 'model_type' => 'Phone', 'buy_date' => '20-Feb-26', 'price' => '979168', 'ram' => '3 GB', 'storage' => '32 GB', 'imei' => '861751069805998'],
            ['brand_id' => 'vivo', 'model_name' => 'Y02', 'model_type' => 'Phone', 'buy_date' => '20-Feb-26', 'price' => '979168', 'ram' => '3 GB', 'storage' => '32 GB', 'imei' => '861751069827018'],

            // REDMI 15C — 4 unit
            ['brand_id' => 'redmi', 'model_name' => '15C', 'model_type' => 'Phone', 'buy_date' => '13-Apr-26', 'price' => '2000000', 'ram' => '6 GB', 'storage' => '128 GB', 'imei' => '868050079149148'],
            ['brand_id' => 'redmi', 'model_name' => '15C', 'model_type' => 'Phone', 'buy_date' => '13-Apr-26', 'price' => '2000000', 'ram' => '6 GB', 'storage' => '128 GB', 'imei' => '868050078137904'],
            ['brand_id' => 'redmi', 'model_name' => '15C', 'model_type' => 'Phone', 'buy_date' => '13-Apr-26', 'price' => '2000000', 'ram' => '6 GB', 'storage' => '128 GB', 'imei' => '868050078876360'],
            ['brand_id' => 'redmi', 'model_name' => '15C', 'model_type' => 'Phone', 'buy_date' => '13-Apr-26', 'price' => '2000000', 'ram' => '6 GB', 'storage' => '128 GB', 'imei' => '868050078784929'],

            // VIVO Y29 — 2 unit (30-Jan-26)
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '30-Jan-26', 'price' => '2057865', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245073968924'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '30-Jan-26', 'price' => '2057865', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866489073233510'],

            // VIVO Y29 — 4 unit (22-Dec-25)
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '22-Dec-25', 'price' => '2303310', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866489076265758'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '22-Dec-25', 'price' => '2303310', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245074201614'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '22-Dec-25', 'price' => '2303310', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245074212694'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '22-Dec-25', 'price' => '2303310', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245078966857'],

            // VIVO Y29 — 3 unit (20-Nov-25)
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '20-Nov-25', 'price' => '2383522', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245076169132'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '20-Nov-25', 'price' => '2383522', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245078829915'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '20-Nov-25', 'price' => '2383522', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245074203297'],

            // SAMSUNG GALAXY S24FE
            ['brand_id' => 'samsung', 'model_name' => 'Galaxy S24FE', 'model_type' => 'Phone', 'buy_date' => '10-Jun-25', 'price' => '7199000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '350109790085794'],

            // OPPO A60 — 4 unit (18-Sep-25)
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Sep-25', 'price' => '1875000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '865174073134097'],
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Sep-25', 'price' => '1875000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '865174077931910'],
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Sep-25', 'price' => '1875000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '865174078674956'],
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Sep-25', 'price' => '1875000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863796073052036'],

            // OPPO A60 — 2 unit (18-Aug-25)
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Aug-25', 'price' => '2300000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866190073427819'],
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Aug-25', 'price' => '2300000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866190077727032'],

            // INFINIX HOT 60 PRO
            ['brand_id' => 'infinix', 'model_name' => 'Hot 60 Pro', 'model_type' => 'Phone', 'buy_date' => '04-Aug-25', 'price' => '2200000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '350348880710594'],

            // REDMI PAD 2 — Tablet (no IMEI)
            ['brand_id' => 'redmi', 'model_name' => 'Pad 2', 'model_type' => 'Tablet', 'buy_date' => '04-Aug-25', 'price' => '2000000', 'ram' => '4 GB', 'storage' => '128 GB'],

            // OPPO A60 (20-Jun-25)
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '20-Jun-25', 'price' => '2300000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866190073198212'],

            // OPPO A60 (12-Mar-25)
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '12-Mar-25', 'price' => '2300000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '8651740775543657'],

            // POCO POCO PAD — Tablet (no IMEI)
            ['brand_id' => 'poco', 'model_name' => 'Poco Pad', 'model_type' => 'Tablet', 'buy_date' => '13-Sep-24', 'price' => '4800000', 'ram' => '8 GB', 'storage' => '256 GB'],

            // SAMSUNG GALAXY TAB S7FE — Tablet (2 unit @ 9.5jt)
            ['brand_id' => 'samsung', 'model_name' => 'Galaxy Tab S7FE', 'model_type' => 'Tablet', 'buy_date' => 'Awal 2022', 'price' => '9500000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '357156430640317'],
            ['brand_id' => 'samsung', 'model_name' => 'Galaxy Tab S7FE', 'model_type' => 'Tablet', 'buy_date' => 'Awal 2022', 'price' => '9500000', 'ram' => '8 GB', 'storage' => '128 GB'],

            // SAMSUNG GALAXY TAB S7FE — 1 unit @ 9.9jt
            ['brand_id' => 'samsung', 'model_name' => 'Galaxy Tab S7FE', 'model_type' => 'Tablet', 'buy_date' => 'Awal 2022', 'price' => '9900000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '357156430410620'],
        ];

        foreach ($devices as $device) {
            $imei = $device['imei'] ?? null;
            $macAddress = $device['mac_address'] ?? null;

            // Generate model_id sesuai format manual register: dev-py02-001, dev-gs24fe-002, ...
            $modelId = PhoneList::generateModelId($device['model_name'], $device['model_type']);

            $phone = PhoneList::updateOrCreate(
                ['model_id' => $modelId],
                [
                    'brand_id' => $device['brand_id'],
                    'model_name' => $device['model_name'],
                    'model_type' => $device['model_type'],
                    'buy_date' => $device['buy_date'],
                    'price' => $device['price'],
                    'ram' => $device['ram'],
                    'storage' => $device['storage'],
                    'imei' => $imei,
                    'mac_address' => $macAddress,
                    'registered' => false,
                    'approved' => true,
                    'hash_token' => null,
                ]
            );

            // Link existing photo files if any
            $photoDir = public_path('storage/device/'.$phone->id);
            if (is_dir($photoDir)) {
                $files = array_values(array_filter(scandir($photoDir), fn ($f) => ! in_array($f, ['.', '..'])));
                if (! empty($files)) {
                    sort($files);
                    $paths = array_map(fn ($f) => 'storage/device/'.$phone->id.'/'.$f, $files);
                    $phone->update([
                        'thumbnail' => $paths[0],
                        'list_photos' => $paths,
                    ]);
                }
            }
        }
    }
}
