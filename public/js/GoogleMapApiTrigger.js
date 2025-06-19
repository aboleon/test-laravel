window.onGoogleMapsApiReady = function() {
  window.GoogleMapApiHelper.googleMapsCallbacks.forEach(function(cbName) {
    if(typeof window[cbName] === 'function') {
      window[cbName]();
    } else {
      console.error("Callback function not found:", cbName);
    }
  });
};