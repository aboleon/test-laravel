{{---
@props([
    'id' => '1',
    'selector' => '.mfw-tab',
])

@push('js')
    <script>

        $(document).ready(function() {

            let selectedTabId = Cookies.get('mfw_tab_redirect_{{$id}}');
            console.log('selectedTabId for selector {{$selector}}', selectedTabId);
            if (selectedTabId) {
                console.log('clicking on tab with id', selectedTabId);
                let jTab = $('#' + selectedTabId);
                let jTabPane = $(jTab.attr("data-bs-target"));
                jTab.trigger('click');
                // for some reason bootstrap sometimes didn't show the tab, so we do it manually
                jTabPane.parent().find('.tab-pane').not(jTabPane).removeClass('active show');
                jTabPane.addClass('active show');
            }

            $('{{ $selector }}').on('shown.bs.tab', function(e) {
                console.log('setting cookie for tab with selector {{$selector}}', $(e.target).attr('id'));
                Cookies.set('mfw_tab_redirect_{{$id}}', $(e.target).attr('id'), {expires: 1});
            });
        });
    </script>
@endpush
---}}
