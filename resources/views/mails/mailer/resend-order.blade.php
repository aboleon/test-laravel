<x-mail-layout :banner="$mailed->data['banner']">
    Relance de votre commande {{$mailed->data['order']->id}}.<br />
    Connectez-vous via <a href="{{$mailed->data['connect_link']}}">ce lien.</a>
</x-mail-layout>
