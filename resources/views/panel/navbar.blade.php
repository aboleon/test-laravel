<nav class="nav navbar-nav float-end">
    <div class="dropdown">

        <button id="user_avatar" class="dropdown-toggle bg-white border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">

            <x-mediaclass::printer :model="Mediaclass::forModel(auth()->user())->first()"
                                   :default="asset('media/logo-black.png')"
                                   class="rounded-circle w-100"
                                   size="md"
                                   :responsive="false"
                                   :alt="Auth::user()->names()"/>
        </button>
        <ul class="m-0 p-0 list-unstyled dropdown-menu" id="my-account">
            <li class="pt-2 pb-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <a class="dropdown-item" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('account.logout') }}
                    </a>
                </form>
            </li>
        </ul>
    </div>
</nav>
