@pushonce('css')
    <link rel="stylesheet" href="{{ asset('vendor/minicolors/jquery.minicolors.css') }}">
@endpushonce

@pushonce('js')
    <script src="{{ asset('vendor/minicolors/jquery.minicolors.min.js') }}"></script>

    <script>
      $(document).ready(function() {
        $('.minicolors').minicolors({
          theme: 'bootstrap',
        });
      });
    </script>
@endpushonce