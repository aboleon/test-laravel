<span class="list-group-item text-danger bg-danger-soft-hover"
      style="cursor: pointer"
      id="logout-trigger">
    <i class="fas fa-sign-out-alt fa-fw me-2"></i>
    {{__('front/ui.logout')}}
</span>
@pushonce('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const logoutTrigger = document.getElementById('logout-trigger');
            const logoutForm = document.getElementById('headerLogoutForm');

            // Attach click event to the span
            logoutTrigger.addEventListener('click', function () {
                logoutForm.submit();
            });
        });
    </script>
@endpushonce
