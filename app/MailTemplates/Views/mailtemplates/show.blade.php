<x-mail-layout :banner="$parsed->banner">
@push('css')
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-bottom: 30px;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            h2 {
                color: #333;
                margin-top: 30px;
                margin-bottom: 15px;
            }
            .group-header {
                background-color: #e8e8e8;
                font-weight: bold;
                text-align: center;
            }
        </style>
@endpush

{!! $parsed->content()['content'] !!}

</x-mail-layout>

