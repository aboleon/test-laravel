<?php

declare(strict_types=1);

namespace App\Generators;

use Artesaos\SEOTools\Facades\JsonLd;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\TwitterCard;
use Illuminate\Support\Str;

class Seo
{
    public static function generator(string $title, ?string $description = null, ?string $image = null): void
    {

        if (!$description) {
            $description = __('front.seo.home_description');
        }

        $description = Str::limit($description, 160); // seo recommendation


        $url = url()->current();

        $title = config('app.name').' - '. $title;

        SEOMeta::setTitle($title, config('app.name'));
        SEOMeta::setDescription($description);
        SEOMeta::setCanonical($url);

        OpenGraph::setDescription($description);
        OpenGraph::setTitle($title);
        OpenGraph::setUrl($url);

        TwitterCard::setTitle($title);
        TwitterCard::setDescription($description);
        TwitterCard::setUrl($url);

        JsonLd::setTitle($title);
        JsonLd::setDescription($description);
        JsonLd::setUrl($url);

        if ($image) {
            OpenGraph::addImage($image);
            TwitterCard::addImage($image);
            JsonLd::addImage($image);
        }

    }

}
