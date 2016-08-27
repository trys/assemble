var assemble = (function() {
  "use strict";

  var key = 'AIzaSyA0RmX6KHUGbE8MPK5x9mVV1HCTul0uziw';

  return {
    init: function() {

      assemble.map.init( 'assemble.map.loaded' );

      assemble.events.init( 'assemble.events.loaded' );

    },

    map: {

      mapElement: '',

      init: function( callback ) {

        assemble.map.mapElement = document.getElementById( 'map' );
        if ( assemble.map.mapElement ) {
          var mapScript = document.createElement( 'script' );
          mapScript.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=' + callback + '&key=' . key;
          document.body.appendChild( mapScript );
        }
      },

      loaded: function() {
        var center = new google.maps.LatLng( assemble.map.mapElement.getAttribute('data-lat'), assemble.map.mapElement.getAttribute('data-lng') ),
            mapOptions = {
              zoom: 14,
              center: center,
              minZoom: 9,
              maxZoom: 19,
              mapTypeControl: false,
              scrollwheel: false
            },
            map = new google.maps.Map( assemble.map.mapElement, mapOptions ),
            marker = new google.maps.Marker(
              {
                position: center,
                map: map,
                visible: true,
                icon: {
                  url: '/assets/images/pin.png'
                }
              }
            ),
            // https://snazzymaps.com/style/151/ultra-light-with-labels
            styles = [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}];

          map.setOptions( { styles: styles } );
      }
    },

    events: {

      init: function( callback ) {
        var findForm = document.getElementById('find-event');
        if ( findForm ) {
          var mapScript = document.createElement( 'script' );
          mapScript.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=' + callback + '&key=' + key;
          document.body.appendChild( mapScript );
        }
      },

      loaded: function() {

        var findForm = document.getElementById('find-event');
        if ( findForm ) {
          
          var findFormToggle = document.getElementById('find-event-toggle');
          findFormToggle.addEventListener('click', function(event) {
            event.preventDefault();
            assemble.helpers.toggleClass(findForm, 'showing');
          });

          var findFormTrigger = document.getElementById('js-find-event');
          var findFormLocation = document.getElementById('location');
          findForm.addEventListener('submit', function(event) {
            event.preventDefault();

            if ( findFormLocation.value ) {
              var oReq = new XMLHttpRequest();
              oReq.addEventListener('load', assemble.events.find);
              oReq.open('POST', 'https://maps.googleapis.com/maps/api/geocode/json?key=' + key + '&address=' + findFormLocation.value );
              oReq.send();
            }
          });

        }

      },

      find: function() {
        var response = JSON.parse( this.responseText );
        var findFormLocation = document.getElementById('location');
        if ( response.status === 'OK' ) {
          var primaryResult = response.results[ 0 ];
          window.location.href = '/event?lat=' + primaryResult.geometry.location.lat + '&lng=' + primaryResult.geometry.location.lng + '&location=' + findFormLocation.value;
        }
      }

    },

    helpers: {

      hasClass: function(el, className) {
        if (el.classList) {
          return el.classList.contains(className);
        } else {
          return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
        }
      },

      addClass: function(el, className) {
        if (el.classList)
          el.classList.add(className);
        else
          el.className += ' ' + className;
      },

      toggleClass: function(el, className) {
        if (el.classList) {
          el.classList.toggle(className);
        } else {
          var classes = el.className.split(' ');
          var existingIndex = -1;
          for (var i = classes.length; i--;) {
            if (classes[i] === className)
            existingIndex = i;
          }

          if (existingIndex >= 0)
            classes.splice(existingIndex, 1);
          else
            classes.push(className);

          el.className = classes.join(' ');
        }
      },

      removeClass: function(el, className) {
        if (el.classList)
          el.classList.remove(className);
        else
          el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
      },

      throttle: function(callback, wait) {
        var time,
        ready = true;
        return function() {
          if(ready) {
            ready = false;
            time = setTimeout(function(){
              time = null;
              ready = true;
              callback.call();
            }, wait);
          }
        }
      }
    }
  }
}());

assemble.init();