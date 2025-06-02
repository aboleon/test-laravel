@props([
    'title' => 'Title of the card',
    'showTitleAction' => false,
    'titleAction' => [],
    'pictoUrl' => asset('front/images/pictograms/registration.png'),
    'notBusyText' => "Nothing to show yet",
    'readMoreUrl' => "#",
    'seeActionUrl' => "#",
    'seeActionWord' => "Voir",
    'items' => [],
    'bottomActions' => [],
    'showReadMore' => false,
    'showSeeAction' => true,
    'showPicto' => false,
    'useCard' => true,
    'showTitle' => true,

])

<div x-data="{busy: true}">
    @if($useCard)
        <div class="card card-body shadow p-4 align-items-start">
            @endif
            @if($showPicto)
                <img class="rounded-1 h-60px"
                     @click="busy = !busy"
                     src="{{$pictoUrl}}"
                     alt="some picto">
            @endif

            @if($showTitle)
                <h5 class="divine-main-color-text card-title mt-3 mb-2 d-flex w-100">
                    <span>{{$title}}</span>
                    @if($showTitleAction)
                        <a href="{{$titleAction['url']}}"
                           class="btn btn-sm btn-primary ms-auto">{{$titleAction["text"]}}</a>
                    @endif
                </h5>
            @endif

            <span x-show="!busy"
                  x-cloak>{!! $notBusyText !!}</span>

            <div x-show="busy" class="w-100">
                <div class="d-flex flex-column gap-3 mt-2 w-100">
                    @foreach($items as $item)
                        <div class="d-flex align-items-start gap-2">
                            <div class="w-100">
                                @if(isset($item['title']))
                                    <h6 class="d-flex justify-content-between align-items-center divine-secondary-color-text text-primary-emphasis mb-0">{{$item['title']}}
                                        @if(isset($item['badge']))
                                            <span class="ms-3 smaller badge {{$item['badge']['class']}}">{{$item['badge']['text']}}</span>
                                        @endif
                                    </h6>
                                @endif
                                @if(isset($item['extra_badges']))
                                    <div>
                                        @foreach($item['extra_badges'] as $badge)
                                            <span class="badge {{$badge['class']}}">{{$badge['text']}}</span>
                                        @endforeach
                                    </div>
                                @endif
                                @if(isset($item['text']))
                                    <p class="small">
                                        {!! $item['text'] !!}
                                    </p>
                                @endif
                            </div>
                            <div class="ms-auto">
                                @if(isset($item['actions']))
                                    @foreach($item['actions'] as $action)
                                        @switch($action['type'])
                                            @case('button')
                                                <button class="ms-auto btn btn-sm {{$action['class']}} smaller">{{$action['title']}}</button>
                                                @break
                                            @case('link')
                                                <a href="{{$action['url']}}"
                                                   class="ms-auto btn btn-sm {{$action['class']}} smaller">{{$action['title']}}</a>
                                                @break
                                            @case('icon')
                                                <a @click.prevent href="#">
                                                    <i class="ms-auto {{$action['class']}}"
                                                       title="{{$action['title']}}"></i>
                                                </a>
                                                @break
                                            @case('text')
                                                <span class="ms-auto small {{$action['class']}}">{{$action['text']}}</span>
                                                @break
                                        @endswitch
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>


            <div class="d-flex justify-content-start align-items-center w-100 gap-2 mt-3">

                @foreach($bottomActions as $action)
                    @switch($action['type'])
                        @case('button')
                            <button class="btn btn-sm {{$action['class']}}">{{$action['title']}}</button>
                            @break
                    @endswitch
                @endforeach

                @if($showSeeAction)
                    <a href="{{$seeActionUrl}}"
                       class="btn btn-sm btn-outline-primary">{{$seeActionWord}}</a>
                @endif
                @if($showReadMore)
                    <a href="{{$readMoreUrl}}"
                       class="btn btn-lg btn-link p-0"><u>{!! __('front/dashboard.learn_more') !!}</u></a>
                @endif

            </div>
            @if($useCard)
        </div>
    @endif
</div>
