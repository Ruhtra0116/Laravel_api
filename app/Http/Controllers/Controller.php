<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if (!$image) {
            return null;
        }

        $filename = time() . '.png';
        // save image
        \Storage::disk($path)->put($filename, base64_decode($image));

        //return the path
        // Use the url() helper function instead of URL::to()
        return url('localhost:8000') . '/storage/' . $path . '/' . $filename;
    }
}
