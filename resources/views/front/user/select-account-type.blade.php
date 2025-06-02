<x-front-layout :event="$event">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <x-front.event-banner :event="$event->withoutRelations()"/>
            </div>
            <div class="mt-3">
                <h1 class="text-center">Me connecter en tant que</h1>
            </div>

            <div class="d-flex justify-content-between">
                <div class="card border flex-grow-1">
                    <div class="card-body">
                        <h5 class="card-title">Individu</h5>
                        <a href="{{route('front.event.login-as-user', [
                            'event' => $event->id,
                        ])}}" class="stretched-link">Choisir</a>
                    </div>
                </div>
                <div class="vertical-sep border mx-1"></div>
                <div class="card border flex-grow-1">
                    <div class="card-body">
                        <h5 class="card-title">Chef de groupe</h5>
                        <a href="{{route('front.event.login-as-group-manager', [
                        'event' => $event->id,
                        ])}}" class="stretched-link">Choisir</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-front-layout>
