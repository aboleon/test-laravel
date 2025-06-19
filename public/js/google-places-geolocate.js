function initialize() {

    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy,
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }

    function setGoogleBar(element) {

        let mapsbar_id = element.attr('id'),
            autocomplete,
            componentForm = {
                street_number: 'long_name',
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'long_name',
                administrative_area_level_2: 'long_name',
                country: 'long_name',
                postal_code: 'short_name',
                regions: 'long_name',
            },
            google_places_params = $('#params_'+mapsbar_id),
            api_options = {types: ['geocode'],};

        if (google_places_params.length) {
            let options = $.trim(google_places_params.text());
            if (options.length) {
                api_options = JSON.parse(options);
            }
        }
        api_options.language = 'fr';
        console.log(api_options,'API Options');
        autocomplete = new google.maps.places.Autocomplete(element.find('.g_autocomplete')[0], api_options);

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();
            console.log(place);
            for (var component in componentForm) {
                element.find('.' + component).val('').prop('disabled', false);
                /* document.getElementById(component).value = '';
                document.getElementById(component).disabled = false; */
            }
            element.find('.country_code').val('').prop('disabled', false);
            //document.getElementById('country_code').disabled = false;
            let address = [];
            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    element.find('.' + addressType).val(val);
                }
                if (addressType == 'street_number' || addressType == 'route') {
                    address.push(place.address_components[i].long_name);
                }
            }

            //setup address 1
            element.find('.address1').val(address.join(", "));

            element.find('.wa_geo_lat').val(place.geometry.location.lat()).change();
            element.find('.wa_geo_lon').val(place.geometry.location.lng()).change();

            var callback = element.parent().data('callback');
            if (callback != undefined) {
                var fn = window[callback];
                if (typeof fn === 'function') {
                    fn(element);
                }
            }
        });

    }

    if ($('.gmapsbar').length) {
        $('.gmapsbar').each(function () {
            setGoogleBar($(this));
        });
    }
}
