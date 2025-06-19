if ('undefined' === typeof window.GoogleMapApiHelper) {
  window.GoogleMapApiHelper = {
    googleMapsCallbacks: [],
    addCallback: function(callback) {
      this.googleMapsCallbacks.push(callback);
    },
  };
}