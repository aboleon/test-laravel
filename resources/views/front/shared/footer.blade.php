<footer class="pt-5 mt-auto">
    <div class="container">
        <!-- Bottom footer -->
        <div class="py-3">
            <div class="container px-0">
                <div class="d-lg-flex justify-content-between align-items-center py-3 text-center text-md-left">

                    <div class="text-body text-primary-hover">&copy; {{ date('Y') }} Divine ID | {{__('front/ui.build_by')}}
                        <a href="https://www.wagaia.com/"
                           target="_blank"
                           class="text-body">Wagaia</a></div>

                    @if(isset($event) && $event->id)

                        <div class="mt-lg-0 mt-3">
                            <a class="me-4"
                               href="{{route('front.event.contact', $event)}}">{{__('front/ui.footer_contact')}}</a>

                            <a class="me-4"
                               href="{{route('front.event.privacy-policy', $event)}}">{{__('front/ui.footer_privacy_policy')}}</a>

                            <a href="#cgv" data-bs-toggle="modal"
                               data-bs-target="#cgvModal">{{__('front/ui.footer_terms_and_conditions')}}</a>
                        </div>


                        @include('front.shared.modal.cgv-modal')

                    @endif


                </div>
            </div>
        </div>
    </div>
</footer>

