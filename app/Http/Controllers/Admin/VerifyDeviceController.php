<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeviceManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class VerifyDeviceController extends Controller
{

    private function fotoAddHandlers(
        Request $request,
        array $data
    ) {

        $device =
            DeviceManagement::findOrFail(
                $data['id']
            );

        $path =
            'storage/device/' .
            $device->device_id;

        $fullpath =
            public_path($path);

        $uploadedPhotos = [];

        /*
        |--------------------------------------------------------------------------
        | delete old folder
        |--------------------------------------------------------------------------
        */

        if (file_exists($fullpath)) {

            File::deleteDirectory(
                $fullpath
            );
        }

        /*
        |--------------------------------------------------------------------------
        | recreate folder
        |--------------------------------------------------------------------------
        */

        mkdir(
            $fullpath,
            0755,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | upload
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            foreach (

                $request->file('image')

                as $photo

            ) {

                $filename =

                    hash_file(
                        'crc32b',
                        $photo->getRealPath()
                    )

                    . '.'

                    . $photo->getClientOriginalExtension();

                $photo->move(
                    $fullpath,
                    $filename
                );

                $uploadedPhotos[] =

                    $path .
                    '/' .
                    $filename;
            }
        }

        return $uploadedPhotos;
    }

    //
    public function verifyDevice(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:device_management,id',
            'comment' => 'nullable|string',
            'image' => 'required|min:1|array|max:4',
            'image.*' => 'required|image|mimes:jpeg,png,jpg,svg|max:5120',
        ]);
        $uploadedPhotos = $this->fotoAddHandlers($request, $data);
        debugbar()->log($uploadedPhotos);
        debugbar()->log($data);

        return response()->json("success", 200);
    }
}
