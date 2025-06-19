class GoogleMapHelper {
  static init(elementSelector, options) {
    const element = document.querySelector(elementSelector);
    if (!element) {
      console.error(`Element with selector ${elementSelector} not found.`);
      return;
    }

    const autocomplete = new google.maps.places.Autocomplete(element);

    autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace();
      if (!place.geometry) {
        console.error('Autocomplete\'s returned place contains no geometry');
        if (options && options.error) {
          options.error(element.value);
        }
        return;
      }

      const address = GoogleMapHelper._getAddressComponents(place, element);

      if (options && options.change) {
        options.change(address, place);
      }
    });

    element.addEventListener('keydown', (event) => {
      if (event.keyCode === 13) {
        event.stopPropagation();
        event.preventDefault();
      }
    });
  }

  static _getAddressComponents(place, element) {
    const address = {
      text_address: element.value,
      street_number: '',
      route: '',
      locality: '',
      postal_code: '',
      country_code: '',
      administrative_area_level_1: '',
      administrative_area_level_1_short: '',
      administrative_area_level_2: '',
      latitude: place.geometry.location.lat(),
      longitude: place.geometry.location.lng(),
    };

    place.address_components.forEach((component) => {
      const types = component.types;
      const value = component.long_name;
      const short_value = component.short_name;

      if (types.includes('street_number')) {
        address.street_number = value;
      } else if (types.includes('route')) {
        address.route = value;
      } else if (types.includes('locality')) {
        address.locality = value;
      } else if (types.includes('postal_code')) {
        address.postal_code = value;
      } else if (types.includes('country')) {
        address.country_code = short_value; // Assuming you want the short code.
      } else if (types.includes('administrative_area_level_1')) {
        address.administrative_area_level_1 = value;
        address.administrative_area_level_1_short = short_value;
      } else if (types.includes('administrative_area_level_2')) {
        address.administrative_area_level_2 = value;
      }
    });

    return address;
  }

  static isContinent(place) {
    return place.types.includes('continent');
  }

  static isCountry(place) {
    return place.types.includes('country');
  }

  static isLocality(place) {
    return place.types.includes('locality');
  }

  static isStreetAddress(place) {
    return place.types.includes('street_address');
  }

  static getPlaceType(place) {
    if (GoogleMapHelper.isContinent(place)) {
      return 'continent';
    } else if (GoogleMapHelper.isCountry(place)) {
      return 'country';
    } else if (GoogleMapHelper.isLocality(place)) {
      return 'locality';
    } else if (GoogleMapHelper.isStreetAddress(place)) {
      return 'street_address';
    } else {
      return 'unknown';
    }
  }

  static getCountryFromStreetAddressPlace(place) {
    let country = null;
    place.address_components.forEach((component) => {
      const types = component.types;
      const value = component.long_name;
      if (types.includes('country')) {
        country = value;
      }
    });
    return country;
  }

}
