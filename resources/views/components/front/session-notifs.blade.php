@props([
    'prefix' => '',
    'useModals' => false,
])


@if(false === $useModals)
    <div {{$attributes->merge(['class' => ""])}}>
        @if (session($prefix . 'success'))
            <div class="alert alert-success rounded-0 fs-14" role="alert">
                {{session($prefix . 'success')}}
            </div>
        @endif
        @if(session($prefix . 'info'))
            <div class="alert alert-info rounded-0 fs-14" role="alert">
                {{ session($prefix . 'info') }}
            </div>
        @endif
        @if (session($prefix . 'warning'))
            <div class="alert alert-warning rounded-0 fs-14" role="alert">
                {{session($prefix . 'warning')}}
            </div>
        @endif
        @if (session($prefix . 'danger'))
            <div class="alert alert-danger rounded-0 fs-14" role="alert">
                {{session($prefix . 'danger')}}
            </div>
        @endif
        @if (session($prefix . 'error'))
            <div class="alert alert-danger rounded-0 fs-14" role="alert">
                {{session($prefix . 'error')}}
            </div>
        @endif
    </div>
@else
    @php
        $errorMsg = session($prefix . 'error');
        $danger = session($prefix . 'danger');
        $warning = session($prefix . 'warning');
        $info = session($prefix . 'info');
        $success = session($prefix . 'success');
    @endphp

    @if($errorMsg)
        <x-front.modal-notif
                :text="$errorMsg"
                type="danger"
        />
    @elseif( $danger )
        <x-front.modal-notif
                :text="$danger"
                type="danger"
        />
    @elseif( $warning )
        <x-front.modal-notif
                :text="$warning"
                type="warning"
        />
    @elseif( $info )
        <x-front.modal-notif
                :text="$info"
                type="info"
        />
    @elseif( $success )
        <x-front.modal-notif
                :text="$success"
                type="success"
        />
    @endif
@endif