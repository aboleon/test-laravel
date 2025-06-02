<script>
    function appendDymanicEstablishment(result) {
        if (!result.hasOwnProperty('error')) {
            $('#establishment-form input:text, textarea').val('');
            let est = $('#profile_establishment_id');
            est.find(':selected').prop('selected', false).change();
            est.append('<option value="' + result.establishment.id + '">' + result.establishment.name + '</option>');
            est.find('option[value=' + result.establishment.id + ']').prop('selected', true).change();
           /* setTimeout(function () {
                let myModal = new bootstrap.Modal(document.getElementById('mfwDynamicModal'));
                myModal.hide();
            }, 2000);*/
        }

    }
</script>
{!! csscrush_inline(public_path('css/establishments.css')) !!}
{!! csscrush_inline(public_path('vendor/mfw/css/fragments/_dynamic_search.css')) !!}
<style>
    #mfwDynamicModal .modal-content {
        width: 800px;
    }
</style>
@include('establishments.form')
<script src="{{ asset('js/establishment_search.js') }}"></script>
@include('mfw-modals.dynamic_google_place_api')
