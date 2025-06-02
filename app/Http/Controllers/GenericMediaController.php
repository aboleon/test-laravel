<?php

namespace App\Http\Controllers;

use App\Models\GenericMedia;

class GenericMediaController extends Controller
{
    public function show()
    {
        $model = new GenericMedia();

        return view('dashboard.generic-media')->with(['model' => $model]);
    }
}
