<?php

namespace App\Http\Controllers\Front;

use App\Generators\Seo;
use App\Http\Controllers\Front\Auth\AuthenticatedSessionController;
use Illuminate\Http\RedirectResponse;
use App\Traits\{Locale};
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController
{
    use Locale;

    public function home(): Renderable
    {
        Seo::generator(__('front/seo.home_title'));
        return view('front.home');
    }
}
