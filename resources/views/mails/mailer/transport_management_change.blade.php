@php

    use \App\Enum\DesiredTransportManagement as T;

        $text = [
        'fr' => "Bonjour, <br>
        Nous venons de procéder au changement de la gestion de votre dossier Transport qui est désormais ".
         (match($mailed->getEventTransport()->desired_management) {
            T::DIVINE->value => 'géré par Divine ID',
            T::PARTICIPANT->value => 'géré par vous-même',
            T::UNNECESSARY->value => 'classé comme ne nécessitant aucune gestion',
         }) . ".<br><br>",
        'en' => "Hello, <br>
        Nous venons de procéder au changement de la gestion de votre dossier Transport qui est désormais".
         (match($mailed->getEventTransport()->desired_management) {
            T::DIVINE->value => 'géré par Divine ID',
            T::PARTICIPANT->value => 'géré par vous-même',
            T::UNNECESSARY->value => 'classé comme ne nécessitant aucune gestion',
         }) . ".<br><br>",
            ];

@endphp

<x-mail-layout :banner="$mailed->data['banner']">

    {!! $text[$mailed->accountLanguage()] !!}

</x-mail-layout>
