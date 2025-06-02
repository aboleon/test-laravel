<x-pdf-layout>
    @push('css')
        {!!  csscrush_inline(public_path('front/css/pdf.css')) !!}
    @endpush

    <table>
        <tr style="height:360px;">
            <td class="logo-container">
                <img
                    src="{{  \App\Helpers\PdfHelper::imageToBase64('assets/pdf/logonew.jpg') }}"
                    alt="divine logo">
            </td>
        </tr>
    </table>

    {!! $parsed->content()['content'] !!}
</x-pdf-layout>
