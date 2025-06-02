<!doctype html>
<html lang="fr-FR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @stack('title')
    <style>
        @font-face {
            font-family: 'Verdana';
            src: url('{{ public_path('fonts/verdana/verdana.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'Verdana';
            src: url('{{ public_path('fonts/verdana/verdana-bold.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        @font-face {
            font-family: 'Verdana';
            src: url('{{ public_path('fonts/verdana/verdana-italic.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: italic;
        }

        @font-face {
            font-family: 'Verdana';
            src: url('{{ public_path('fonts/verdana/verdana-bold-italic.ttf') }}') format('truetype');
            font-weight: bold;
            font-style: italic;
        }

    </style>
    @stack('css')
</head>

<body>
{{ $slot }}
</body>
</html>
