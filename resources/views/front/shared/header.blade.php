@php
    use App\Accessors\Front\FrontCache;

    if (!function_exists('strip_locale_from_path')) {
        function strip_locale_from_path($path, $locales = ['en', 'fr']) {
            $segments = explode('/', $path);
            if (in_array($segments[0], $locales)) {
                array_shift($segments);
            }
            return implode('/', $segments);
        }
    }
    $pathWithoutLocale = strip_locale_from_path(request()->path());
    $locale = app()->getLocale();
    $eventContact = FrontCache::getEventContact();
    $event = FrontCache::getEvent();

@endphp
<div class="container-xl fixed-top">
    <div class="top-bar-thin divine-main-color fs-14 mb-3 d-flex align-items-center justify-content-between"
         id="front-topbar">

        <div class="d-flex gap-2">
            <a href="/" class="text-body-secondary"><i class="fas fa-house"></i></a>
            @if(isset($event))

                @foreach($event->flags as $flag)
                    @php
                        $word = match($flag) {
                            'en' => 'English',
                            'fr' => 'Français',
                            default => 'Unknown',
                        };
                    @endphp

                    <a class="dropdown-item"
                       href="{{ url($flag . '/' . $pathWithoutLocale) }}">
                        <img src="{{ asset('front/images/flags/' . $flag . '.png') }}"
                             alt="{{$flag}} flag"
                             class="me-1"/>
                    </a>
                @endforeach

            @endif
        </div>

        <div class="d-flex align-items-center gap-2">
            @if(auth()->check() && auth()->user()->type == \App\Enum\UserType::ACCOUNT->value)
                <div class="dropdown">
                    <i class="dropdown-toggle fas fa-user" data-bs-toggle="dropdown"></i>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if(isset($event) && $event->id)
                            @if(isset($isGroupManager) && !$isGroupManager)
                                <li><a href="{{route('front.event.dashboard', [
                                            'locale' => app()->getLocale()??'fr',
                                            'event' => $event,
                                        ])}}"
                                       class="dropdown-item">
                                        <i class="bi bi-person-circle me-1"></i>
                                        {{__('front/ui.my_account')}}
                                    </a>
                                </li>
                            @endif
                        @endif
                        <li>
                            <div x-data class="dropdown-item">
                                <form id="headerLogoutForm"
                                      x-ref="headerLogoutForm"
                                      action="{{route('front.event.logout', ['locale' => app()->getLocale(), 'event' => $event])}}"
                                      method="post"
                                      class="d-inline">
                                    @if (isset($event) && $event instanceof \App\Models\Event && $event->id)
                                        <input type="hidden" name="redirect_to"
                                               value="{{ \App\Accessors\EventAccessor::getEventFrontUrl($event) }}">
                                    @endif
                                    @csrf
                                    <a @click.prevent="$refs.headerLogoutForm.submit()"
                                       class="list-group-item text-danger-emphasis"
                                       href="#">
                                        <i class="fas fa-sign-out-alt fa-fw me-1"></i>
                                        {{__('front/ui.logout')}}
                                    </a>
                                </form>
                            </div>

                        </li>
                    </ul>
                </div>
            @endif


            @auth
                @if (isset($isConnectedAsManager))
                    @if($isConnectedAsManager && session('front_group_manager_id') == $eventContact->id)
                        <div class="dropdown popup-cart-btn-container">
                            <a class="dropdown-toggle d-flex gap-2 align-items-center position-relative"
                               id="popup-cart-button"
                               href="{{ route('front.event.group.checkout', $event) }}">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        </div>
                    @else
                        <livewire:front.cart.popup-cart :event-contact="$eventContact->setRelations([])"
                                                        :is-group-manager="$isGroupManager"/>
                    @endif
                @endif
            @endauth
        </div>
    </div>
</div>
<br>

<x-front.session-notifs :use-modals="true" prefix="general."/>


@push("js")
    <script>
        window.Trail = {
            animationSpeed: 400,
            nbTrails: 10,
            trigger: function (e) {

                let jCartBtn = $('#popup-cart-button');
                let animationSpeed = this.animationSpeed;
                let nbTrails = this.nbTrails;

                let mouseX = e.pageX;
                let mouseY = e.pageY;

                let trail = $('<div class="trail position-absolute"></div>').appendTo('body');

                trail.css({
                    left: mouseX,
                    top: mouseY,
                });

                trail.animate({
                    left: jCartBtn.offset().left,
                    top: jCartBtn.offset().top,
                }, animationSpeed, function () {
                    trail.remove();

                });

                let inBetweenTrailSpeed = animationSpeed / nbTrails;

                for (let i = 0; i < nbTrails; i++) {
                    setTimeout(function () {
                        let trailClone = trail.clone().addClass('trail-effect trail-effect-' + i);
                        trailClone.appendTo('body');
                        trailClone.animate({
                            opacity: 0,
                            left: jCartBtn.offset().left,
                            top: jCartBtn.offset().top,
                        }, animationSpeed, function () {
                            trailClone.remove();
                        });
                    }, inBetweenTrailSpeed * i);
                }
            },
        };

        window.cartTimerIntervalId = null;
        window.cartTimer = function (expiry) {

            return {
                expiry: expiry,
                remaining: null,
                init() {
                    if (window.cartTimerIntervalId) {
                        clearInterval(window.cartTimerIntervalId);
                    }
                    this.setRemaining();
                    window.cartTimerIntervalId = setInterval(() => {
                        this.setRemaining();
                    }, 1000);

                },
                setRemaining() {
                    const diff = this.expiry - new Date().getTime();
                    this.remaining = parseInt(diff / 1000);
                    if (this.remaining < 0) {
                        clearInterval(window.cartTimerIntervalId);
                    }
                },
                days() {
                    return {
                        value: this.remaining / 86400,
                        remaining: this.remaining % 86400,
                    };
                },
                hours() {
                    return {
                        value: this.days().remaining / 3600,
                        remaining: this.days().remaining % 3600,
                    };
                },
                minutes() {
                    return {
                        value: this.hours().remaining / 60,
                        remaining: this.hours().remaining % 60,
                    };
                },
                seconds() {
                    return {
                        value: this.minutes().remaining,
                    };
                },
                format(value) {
                    return ('0' + parseInt(value)).slice(-2);
                },
                time() {
                    return {
                        days: this.format(this.days().value),
                        hours: this.format(this.hours().value),
                        minutes: this.format(this.minutes().value),
                        seconds: this.format(this.seconds().value),
                    };
                },
            };
        };


    </script>

    <script>
        $(document).ready(function () {
            $(document).click(function (e) {
                let jTarget = $(e.target);
                if (jTarget.closest('.popup-cart-btn-container').length === 0) {
                    if (jTarget.closest('.popup-cart').length === 0) {
                        $('.popup-cart').removeClass('show');
                    }
                }
            });
        });
    </script>
@endpush
