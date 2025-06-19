function simplified_tinymce_settings(targets) {
    var baseHref = location.protocol + '//' + window.location.hostname + '/';
    return {
        selector: targets,
        theme: "silver",
        width: '100%',
        menubar: false,
        entity_encoding: "raw",
        plugins: [
            "advlist autolink autosave link lists charmap print preview hr anchor pagebreak spellchecker",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
            "table directionality emoticons template paste"
        ],
        toolbar1: "formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | cut copy paste | bullist numlist | outdent indent blockquote | undo redo | image media responsivefilemanager | link unlink code",
        image_advtab: true,
        language: "fr_FR",
        document_base_url: baseHref,
        relative_urls: false,
        remove_script_host: true,
        language_url: baseHref + "js/tinymce/langs/fr_FR.js",
        external_filemanager_path: baseHref + 'vendor/filemanager/',
        filemanager_title: 'Filemanager',
        external_plugins: {
            'filemanager': baseHref + 'vendor/filemanager/plugin.min.js',
            'responsivefilemanager': baseHref + 'vendor/responsivefilemanager/plugin.min.js',
        }
    }
}
