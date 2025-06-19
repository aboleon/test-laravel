function default_tinymce_settings(targets) {
    var baseHref = location.protocol + '//' + window.location.hostname + '/';
    return {
        selector: targets,
        theme: 'silver',
        width: '100%',
        height: 480,
        menubar: false,
        entity_encoding: 'raw',
        branding: false,
        plugins: [
            'advlist autolink autosave link image lists charmap print preview hr anchor pagebreak spellchecker',
            'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
            'table directionality emoticons template paste textpattern',
        ],
        // | forecolor backcolor
        toolbar1: 'bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | styleselect formatselect fontsizeselect',
        toolbar2: 'cut copy paste | searchreplace | bullist numlist | outdent indent blockquote | undo redo | link unlink anchor image media responsivefilemanager | insertdatetime preview',
        toolbar3: 'table | hr removeformat | subscript superscript | fullscreen | ltr rtl | spellchecker | nonbreaking restoredraft code',
        image_advtab: true,
        language: 'fr_FR',
        language_url: baseHref + 'js/tinymce/langs/fr_FR.js',
        document_base_url: baseHref,
        relative_urls: false,
        remove_script_host: true,
        //content_css:baseHref+"css/style_tiny.css",
        external_filemanager_path: baseHref + 'vendor/filemanager/',
        filemanager_title: 'Filemanager',
        external_plugins: {
            'filemanager': baseHref + 'vendor/filemanager/plugin.min.js',
            'responsivefilemanager': baseHref + 'vendor/responsivefilemanager/plugin.min.js',
        },
        setup: function (editor) {
            editor.on('change', function () {
                tinymce.triggerSave();
            });
        },
    };
}
