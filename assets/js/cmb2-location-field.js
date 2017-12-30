function init_cmb2_location_map() {
  var elements = {
    mapElem: document.getElementById('cmb2-location-field-map'),
    latElem: document.getElementById('_doin_latlng_lat'),
    lngElem: document.getElementById('_doin_latlng_lng'),
    addressElem: document.getElementById('_doin_latlng_address')
  }

  if (!elements.mapElem || !elements.latElem || !elements.lngElem || !elements.addressElem ) {
    return;
  }


  var latLng = new google.maps.LatLng(38.838283, -9.267047);
  window.map = new google.maps.Map(elements.mapElem, {
    center: latLng,
    zoom: 2
  });

  if (elements.latElem.value && elements.lngElem.value) {
    var markerLatLng = new google.maps.LatLng(parseFloat(elements.latElem.value), parseFloat(elements.lngElem.value));
    window.marker = new google.maps.Marker({
      position: markerLatLng,
      map: map
    });

    map.panTo(markerLatLng)
    map.setZoom(10)
  }

  // Autocomplete
  autocomplete = new google.maps.places.Autocomplete(
      /** @type {!HTMLInputElement} */(elements.addressElem),
      {types: ['geocode']});

  // When the user selects an address from the dropdown, populate the address
  // fields in the form.
  autocomplete.addListener('place_changed', function() {
    var place = autocomplete.getPlace();
    createMarker(place.geometry.location, elements)
  });


  google.maps.event.addListener(map, 'click', function (e) {
    //Determine the location where the user has clicked.
    var location = e.latLng;

    createMarker(location, elements)
  });
}

function createMarker(location, elements) {
  if (window.marker) {
    window.marker.setMap(null);
  }

  //Create a marker and placed it on the map.
  window.marker = new google.maps.Marker({
    position: location,
    map: window.map
  });

  window.map.panTo(location)

  // Add location to hidden fields

  elements.latElem.value = location.lat()
  elements.lngElem.value = location.lng()
}
