<script>
    function appendDymanicPlace(result) {
        if (!result.hasOwnProperty('error')) {
            $('#place-form input:text, #place-form textarea').val('');
            let est = $('#'+result.selectable);
            est.find(':selected').prop('selected', false).change();
            est.append('<option value="' + result.place_id + '">' + result.title + '</option>');
            est.find('option[value=' + result.place_id + ']').prop('selected', true).change();
            setTimeout(function () {
                $('#mfwDynamicModal').modal('hide');
                $('.modal-backdrop, .pac-container').remove();
            }, 2000);
        }
    }
</script>
<style>
    #mfwDynamicModal .modal-content {
        width: 800px;
    }
</style>
@include('places.form')
@include('mfw-modals.dynamic_google_place_api')

<div id="mediaclass_dynamic">
</div>

<script>

    function loadStylesheet(url) {
        $('<link>')
            .appendTo('#mediaclass_dynamic')
            .attr({
                type: 'text/css',
                rel: 'stylesheet',
                href: url,
            });
    }

    if (typeof MediaclassUploader === 'undefined') {
        $.when(
            loadStylesheet('{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/css/jquery.fileupload.css') !!}'),
            loadStylesheet('{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/css/jquery.fileupload-ui.css') !!}'),
            loadStylesheet('{!! asset('vendor/mfw/mediaclass/css/styles.crush.css') !!}'),

            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/vendor/jquery.ui.widget.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/tmpl.min.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/load-image.all.min.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/jquery.iframe-transport.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/jquery.fileupload.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/jquery.fileupload-process.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/jquery.fileupload-image.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/jquery.fileupload-validate.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/jQuery-File-Upload/js/jquery.fileupload-ui.js') !!}"),
            $.getScript("{!! asset('vendor/mfw/mediaclass/uploader.js') !!}"),
        ).done(
            setTimeout(function () {
                MediaclassUploader.init();
                $('#mfwDynamicModal input[name=\'mediaclass_temp_id\']').val($('input[name=\'mediaclass_temp_id\']').first().val());
            }, 1000),
        )
            .fail(function () {
                console.error('One or more Mediaclass scripts failed to load');
            });

    } else {
        setTimeout(function () {
            MediaclassUploader.init();
            $('#mfwDynamicModal input[name=\'mediaclass_temp_id\']').val($('input[name=\'mediaclass_temp_id\']').first().val());

        }, 1000);
    }
</script>
