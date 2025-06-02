<?php

namespace App\Helpers;

use Throwable;

class Translations
{

        public static function translationsState(object $model, string $property): array
        {
            $parsed_translations = [];
            foreach(config('mfw.translatable.active_locales') as $locale) {
                $parsed_translations[$locale] = 'Non traduit en '. __('lang.'.$locale.'.label');
            }
            try {
                $translations = $model->getTranslations($property);
                foreach(config('mfw.translatable.active_locales') as $locale) {
                    $parsed_translations[$locale] = $translations[$locale] ?? 'Non traduit en '. __('lang.'.$locale.'.label');
                }
            } catch (Throwable) {
            }

            return $parsed_translations;
        }
}
