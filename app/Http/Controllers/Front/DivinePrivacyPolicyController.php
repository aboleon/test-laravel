<?php

namespace App\Http\Controllers\Front;

use App\Generators\Seo;
use App\Models\Event;
use App\Traits\{Locale};
use Illuminate\Contracts\Support\Renderable;

class DivinePrivacyPolicyController
{
    use Locale;

    public function show(string $locale, Event $event): Renderable
    {
        Seo::generator(__('front/seo.privacy_policy_title'));
        return view('front.privacy-policy', [
            'event' => $event,
        ]);
    }
}
