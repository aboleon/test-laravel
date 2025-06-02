@php
    //    $media = Mediaclass::forGhostModel(\App\Models\GenericMedia::class);

    use App\Models\GenericMedia;
    d(Mediaclass::ghostUrl(GenericMedia::class, 'banner_medium'));
    exit;
@endphp
<x-mediaclass::printer :model="$model"
                       :responsive="false"/>
