@include('mailtemplates.tinycdn')
<script>
    var baseHref = location.protocol + '//' + window.location.hostname + '/';

    function scg_settings() {
        return {
            selector: 'textarea',
            theme: 'silver',
            height: 1000,
            width: '100%',
            menubar: false,
            entity_encoding: 'raw',
            plugins: 'advlist autolink autosave link code lists media searchreplace anchor wordcount fullscreen nonbreaking table directionality',
            toolbar1: 'blocks fontsize | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | cut copy paste | bullist numlist | outdent indent blockquote | undo redo | link unlink code | {{ collect(\App\MailTemplates\Config::activeGroups())->map(fn($item) => 'wg-'.strtolower((new \ReflectionClass($item))->getShortName()))->join(' | ') }}',
            image_advtab: true,
            language: 'fr_FR',
            language_url: baseHref + 'vendor/mfw/js/tinymce/langs/fr_FR.js',
            setup: function (editor) {
                @foreach(\App\MailTemplates\Config::activeGroups() as $item)
                editor.ui.registry.addSplitButton('{{ 'wg-'.strtolower((new \ReflectionClass($item))->getShortName()) }}', {
                    icon: '{!! $item::icon() !!}',
                    tooltip: '{{ $item::title() }}',
                    onAction: function () {
                    },
                    onItemAction: function (api, value) {
                        if (!value.startsWith('__TITLE__')) {
                            editor.insertContent(value);
                        }
                    },
                    fetch: function (callback) {
                        var items = [
                            // Title as a menu item - not clickable
                            {
                                type: 'choiceitem',
                                text: '{{ mb_strtoupper($item::title(), 'UTF-8') }}',
                                onAction: function() {},
                                enabled: false
                            },
                            @foreach ($item::variables() as $key => $variable)
                                {!! '{
                                          type: \'choiceitem\',
                                          text: \'' . addSlashes($key) . '\',
                                          value: \'{' . $variable . '}\'
                                        },'  !!}
                                @endforeach

                        ];
                        callback(items);
                    },
                });
                @endforeach
            },
        };
    }

    tinymce.init(scg_settings());
</script>
