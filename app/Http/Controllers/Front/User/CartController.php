<?php

namespace App\Http\Controllers\Front\User;

use App\Accessors\Front\FrontCache;
use App\Accessors\Front\FrontCartAccessor;
use App\Generators\Seo;
use App\Http\Controllers\Front\EventBaseController;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class CartController extends EventBaseController
{
    public function edit(string $locale, Event $event)
    {
        Seo::generator(__('front/seo.cart'));
        $user   = Auth::getUser();
        $ec     = FrontCache::getEventContact();
        $status = request('status');


        // paybox return code
        // integration manual p45
        $returnType = session('return_type');

        if ($returnType) {
            $simpleReturnCode = 'error';
            switch ($returnType) {
                case "success":
                    $returnCode = session('return_code');
                    if ($returnCode == '00000') {
                        $simpleReturnCode = 'paid';
                    }
                    break;
                case "cancelled":
                    $simpleReturnCode = 'cancelled';
                    break;
                case "rejected":
                    $simpleReturnCode = 'rejected';
                    break;
            }
            return redirect()->route('front.event.cart.edit', [
                'locale' => $locale,
                'event'  => $event,
                'status' => $simpleReturnCode,
            ]);
        }

        $cartAccessor = new FrontCartAccessor();

        return view('front.user.cart', [
            'cartAccessor' => $cartAccessor,
            'cart'         => $cartAccessor->getCart(),
            "user"         => $user,
            "event"        => $event,
            "eventContact" => $ec,
            "status"       => $status,
        ]);
    }
}
