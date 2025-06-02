@pushonce('css')
    <link rel="stylesheet" href="{{ asset('vendor/bs-stepper/bs-stepper.min.css') }}">
@endpushonce

@pushonce('js')
    <script src="{{ asset('vendor/bs-stepper/bs-stepper.min.js') }}"></script>


    <script>

      $(document).ready(function () {
        var stepper = new Stepper($('.bs-stepper')[0], {
          linear: false,
          animation: true,
        });
        stepper.to(2);
        stepper.to(1);
      });
    </script>
@endpushonce