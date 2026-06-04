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
            ['brand_id' => 'vivo', 'model_name' => 'Y02', 'model_type' => 'Phone', 'buy_date' => '20-Feb-26', 'price' => '979168', 'ram' => '3 GB', 'storage' => '32 GB', 'imei' => '861751069207112', 'mac_address' => 'f4:63:fc:45:a4:8e'],
            ['brand_id' => 'vivo', 'model_name' => 'Y02', 'model_type' => 'Phone', 'buy_date' => '20-Feb-26', 'price' => '979168', 'ram' => '3 GB', 'storage' => '32 GB', 'imei' => '861751069805998', 'mac_address' => '3c:a2:c3:87:6e:a1'],
            ['brand_id' => 'vivo', 'model_name' => 'Y02', 'model_type' => 'Phone', 'buy_date' => '20-Feb-26', 'price' => '979168', 'ram' => '3 GB', 'storage' => '32 GB', 'imei' => '861751069827018', 'mac_address' => '3c:a2:c3:87:66:6c'],

            // REDMI 15C — 4 unit
            ['brand_id' => 'redmi', 'model_name' => '15C', 'model_type' => 'Phone', 'buy_date' => '13-Apr-26', 'price' => '2000000', 'ram' => '6 GB', 'storage' => '128 GB', 'imei' => '868050079149148'],
            ['brand_id' => 'redmi', 'model_name' => '15C', 'model_type' => 'Phone', 'buy_date' => '13-Apr-26', 'price' => '2000000', 'ram' => '6 GB', 'storage' => '128 GB', 'imei' => '868050078137904'],
            ['brand_id' => 'redmi', 'model_name' => '15C', 'model_type' => 'Phone', 'buy_date' => '13-Apr-26', 'price' => '2000000', 'ram' => '6 GB', 'storage' => '128 GB', 'imei' => '868050078876360'],
            ['brand_id' => 'redmi', 'model_name' => '15C', 'model_type' => 'Phone', 'buy_date' => '13-Apr-26', 'price' => '2000000', 'ram' => '6 GB', 'storage' => '128 GB', 'imei' => '868050078784929', 'mac_address' => 'a4:c7:88:5c:99:47'],

            // VIVO Y29 — 2 unit (30-Jan-26)
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '30-Jan-26', 'price' => '2057865', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245073968924', 'mac_address' => 'e4:96:52:c0:d6:d3'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '30-Jan-26', 'price' => '2057865', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866489073233510', 'mac_address' => 'e4:41:d4:12:29:68'],

            // VIVO Y29 — 4 unit (22-Dec-25)
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '22-Dec-25', 'price' => '2303310', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866489076265758', 'mac_address' => '6c:40:e8:a5:33:81'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '22-Dec-25', 'price' => '2303310', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245074201614', 'mac_address' => 'e4:96:52:be:7d:4f'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '22-Dec-25', 'price' => '2303310', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245074212694', 'mac_address' => 'e4:96:52:be:55:d3'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '22-Dec-25', 'price' => '2303310', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245078966857', 'mac_address' => '34:05:57:e0:d9:63'],

            // VIVO Y29 — 3 unit (20-Nov-25)
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '20-Nov-25', 'price' => '2383522', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245076169132', 'mac_address' => 'bc:98:29:fc:ec:4e'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '20-Nov-25', 'price' => '2383522', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245078829915', 'mac_address' => '34:05:57:e1:80:c9'],
            ['brand_id' => 'vivo', 'model_name' => 'Y29', 'model_type' => 'Phone', 'buy_date' => '20-Nov-25', 'price' => '2383522', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863245074203297', 'mac_address' => 'e4:96:52:be:75:9f'],

            // SAMSUNG GALAXY S24FE
            ['brand_id' => 'samsung', 'model_name' => 'Galaxy S24FE', 'model_type' => 'Phone', 'buy_date' => '10-Jun-25', 'price' => '7199000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '350109790085794'],

            // OPPO A60 — 4 unit (18-Sep-25)
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Sep-25', 'price' => '1875000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '865174073134097', 'mac_address' => '00:ca:e0:ad:99:fd'],
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Sep-25', 'price' => '1875000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '865174077931910'],
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Sep-25', 'price' => '1875000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '865174078674956', 'mac_address' => 'd0:20:dd:ba:46:27'],
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Sep-25', 'price' => '1875000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '863796073052036'],

            // OPPO A60 — 2 unit (18-Aug-25)
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Aug-25', 'price' => '2300000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866190073427819'],
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '18-Aug-25', 'price' => '2300000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866190077727032', 'mac_address' => '5c:16:48:e0:2b:e7'],

            // INFINIX HOT 60 PRO
            ['brand_id' => 'infinix', 'model_name' => 'Hot 60 Pro', 'model_type' => 'Phone', 'buy_date' => '04-Aug-25', 'price' => '2200000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '350348880710594'],

            // REDMI PAD 2 — Tablet
            ['brand_id' => 'redmi', 'model_name' => 'Pad 2', 'model_type' => 'Tablet', 'buy_date' => '04-Aug-25', 'price' => '2000000', 'ram' => '4 GB', 'storage' => '128 GB', 'mac_address' => '18:67:ef:6a:be:07'],

            // OPPO A60 (20-Jun-25)
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '20-Jun-25', 'price' => '2300000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '866190073198212'],

            // OPPO A60 (12-Mar-25)
            ['brand_id' => 'oppo', 'model_name' => 'A60', 'model_type' => 'Phone', 'buy_date' => '12-Mar-25', 'price' => '2300000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '8651740775543657', 'mac_address' => '5c:16:48:d3:af:45'],

            // POCO POCO PAD — Tablet
            ['brand_id' => 'poco', 'model_name' => 'Poco Pad', 'model_type' => 'Tablet', 'buy_date' => '13-Sep-24', 'price' => '4800000', 'ram' => '8 GB', 'storage' => '256 GB', 'mac_address' => '3c:af:b7:de:5a:bb'],

            // SAMSUNG GALAXY TAB S7FE — Tablet (2 unit @ 9.5jt)
            ['brand_id' => 'samsung', 'model_name' => 'Galaxy Tab S7FE', 'model_type' => 'Tablet', 'buy_date' => 'Awal 2022', 'price' => '9500000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '357156430640317'],
            ['brand_id' => 'samsung', 'model_name' => 'Galaxy Tab S7FE', 'model_type' => 'Tablet', 'buy_date' => 'Awal 2022', 'price' => '9500000', 'ram' => '8 GB', 'storage' => '128 GB', 'imei' => '357156430136878'],

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
