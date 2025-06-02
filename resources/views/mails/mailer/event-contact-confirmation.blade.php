<x-mail-layout :banner="$mailed->data['banner']">
    {!! __('ui.send_event_contact_confirmation.content') !!}<br />
    <a href="{{$mailed->data['link']}}" target="_blank">{{__('ui.send_event_contact_confirmation.link_text')}}</a><br />
    <br />
    {{__('ui.send_event_contact_confirmation.outro')}}
</x-mail-layout>
