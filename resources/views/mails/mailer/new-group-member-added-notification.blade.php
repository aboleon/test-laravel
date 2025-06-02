<x-mail-layout :banner="$mailed->banner">
    {!! __('front/groups.connect_to_attached_user', ['event' => $mailed->event, 'link' => $mailed->autoConnectUrl, 'user' => $mailed->account]) !!}
</x-mail-layout>
