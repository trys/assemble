/**
 * Simple, lightweight, usable local autocomplete library for modern browsers
 * Because there weren’t enough autocomplete scripts in the world? Because I’m completely insane and have NIH syndrome? Probably both. :P
 * @author Lea Verou http://leaverou.github.io/awesomplete
 * MIT license
 */

(function () {

var _ = function (input, o) {
  var me = this;

  // Setup

  this.isOpened = false;

  this.input = $(input);
  this.input.setAttribute("autocomplete", "off");
  this.input.setAttribute("aria-autocomplete", "list");

  o = o || {};

  configure(this, {
    minChars: 2,
    maxItems: 10,
    autoFirst: false,
    data: _.DATA,
    filter: _.FILTER_CONTAINS,
    sort: _.SORT_BYLENGTH,
    item: _.ITEM,
    replace: _.REPLACE
  }, o);

  this.index = -1;

  // Create necessary elements

  this.container = $.create("div", {
    className: "awesomplete",
    around: input
  });

  this.ul = $.create("ul", {
    hidden: "hidden",
    inside: this.container
  });

  this.status = $.create("span", {
    className: "visually-hidden",
    role: "status",
    "aria-live": "assertive",
    "aria-relevant": "additions",
    inside: this.container
  });

  // Bind events

  $.bind(this.input, {
    "input": this.evaluate.bind(this),
    "blur": this.close.bind(this, { reason: "blur" }),
    "keydown": function(evt) {
      var c = evt.keyCode;

      // If the dropdown `ul` is in view, then act on keydown for the following keys:
      // Enter / Esc / Up / Down
      if(me.opened) {
        if (c === 13 && me.selected) { // Enter
          evt.preventDefault();
          me.select();
        }
        else if (c === 27) { // Esc
          me.close({ reason: "esc" });
        }
        else if (c === 38 || c === 40) { // Down/Up arrow
          evt.preventDefault();
          me[c === 38? "previous" : "next"]();
        }
      }
    }
  });

  $.bind(this.input.form, {"submit": this.close.bind(this, { reason: "submit" })});

  $.bind(this.ul, {"mousedown": function(evt) {
    var li = evt.target;

    if (li !== this) {

      while (li && !/li/i.test(li.nodeName)) {
        li = li.parentNode;
      }

      if (li && evt.button === 0) {  // Only select on left click
        evt.preventDefault();
        me.select(li, evt.target);
      }
    }
  }});

  if (this.input.hasAttribute("list")) {
    this.list = "#" + this.input.getAttribute("list");
    this.input.removeAttribute("list");
  }
  else {
    this.list = this.input.getAttribute("data-list") || o.list || [];
  }

  _.all.push(this);
};

_.prototype = {
  set list(list) {
    if (Array.isArray(list)) {
      this._list = list;
    }
    else if (typeof list === "string" && list.indexOf(",") > -1) {
        this._list = list.split(/\s*,\s*/);
    }
    else { // Element or CSS selector
      list = $(list);

      if (list && list.children) {
        var items = [];
        slice.apply(list.children).forEach(function (el) {
          if (!el.disabled) {
            var text = el.textContent.trim();
            var value = el.value || text;
            var label = el.label || text;
            if (value !== "") {
              items.push({ label: label, value: value });
            }
          }
        });
        this._list = items;
      }
    }

    if (document.activeElement === this.input) {
      this.evaluate();
    }
  },

  get selected() {
    return this.index > -1;
  },

  get opened() {
    return this.isOpened;
  },

  close: function (o) {
    if (!this.opened) {
      return;
    }

    this.ul.setAttribute("hidden", "");
    this.isOpened = false;
    this.index = -1;

    $.fire(this.input, "awesomplete-close", o || {});
  },

  open: function () {
    this.ul.removeAttribute("hidden");
    this.isOpened = true;

    if (this.autoFirst && this.index === -1) {
      this.goto(0);
    }

    $.fire(this.input, "awesomplete-open");
  },

  next: function () {
    var count = this.ul.children.length;
    this.goto(this.index < count - 1 ? this.index + 1 : (count ? 0 : -1) );
  },

  previous: function () {
    var count = this.ul.children.length;
    var pos = this.index - 1;

    this.goto(this.selected && pos !== -1 ? pos : count - 1);
  },

  // Should not be used, highlights specific item without any checks!
  goto: function (i) {
    var lis = this.ul.children;

    if (this.selected) {
      lis[this.index].setAttribute("aria-selected", "false");
    }

    this.index = i;

    if (i > -1 && lis.length > 0) {
      lis[i].setAttribute("aria-selected", "true");
      this.status.textContent = lis[i].textContent;

      $.fire(this.input, "awesomplete-highlight", {
        text: this.suggestions[this.index]
      });
    }
  },

  select: function (selected, origin) {
    if (selected) {
      this.index = $.siblingIndex(selected);
    } else {
      selected = this.ul.children[this.index];
    }

    if (selected) {
      var suggestion = this.suggestions[this.index];

      var allowed = $.fire(this.input, "awesomplete-select", {
        text: suggestion,
        origin: origin || selected
      });

      if (allowed) {
        this.replace(suggestion);
        this.close({ reason: "select" });
        $.fire(this.input, "awesomplete-selectcomplete", {
          text: suggestion
        });
      }
    }
  },

  evaluate: function() {
    var me = this;
    var value = this.input.value;

    if (value.length >= this.minChars && this._list.length > 0) {
      this.index = -1;
      // Populate list with options that match
      this.ul.innerHTML = "";

      this.suggestions = this._list
        .map(function(item) {
          return new Suggestion(me.data(item, value));
        })
        .filter(function(item) {
          return me.filter(item, value);
        })
        .sort(this.sort)
        .slice(0, this.maxItems);

      this.suggestions.forEach(function(text) {
          me.ul.appendChild(me.item(text, value));
        });

      if (this.ul.children.length === 0) {
        this.close({ reason: "nomatches" });
      } else {
        this.open();
      }
    }
    else {
      this.close({ reason: "nomatches" });
    }
  }
};

// Static methods/properties

_.all = [];

_.FILTER_CONTAINS = function (text, input) {
  return RegExp($.regExpEscape(input.trim()), "i").test(text);
};

_.FILTER_STARTSWITH = function (text, input) {
  return RegExp("^" + $.regExpEscape(input.trim()), "i").test(text);
};

_.SORT_BYLENGTH = function (a, b) {
  if (a.length !== b.length) {
    return a.length - b.length;
  }

  return a < b? -1 : 1;
};

_.ITEM = function (text, input) {
  var html = input === '' ? text : text.replace(RegExp($.regExpEscape(input.trim()), "gi"), "<mark>$&</mark>");
  return $.create("li", {
    innerHTML: html,
    "aria-selected": "false"
  });
};

_.REPLACE = function (text) {
  this.input.value = text.value;
};

_.DATA = function (item/*, input*/) { return item; };

// Private functions

function Suggestion(data) {
  var o = Array.isArray(data)
    ? { label: data[0], value: data[1] }
    : typeof data === "object" && "label" in data && "value" in data ? data : { label: data, value: data };

  this.label = o.label || o.value;
  this.value = o.value;
}
Object.defineProperty(Suggestion.prototype = Object.create(String.prototype), "length", {
  get: function() { return this.label.length; }
});
Suggestion.prototype.toString = Suggestion.prototype.valueOf = function () {
  return "" + this.label;
};

function configure(instance, properties, o) {
  for (var i in properties) {
    var initial = properties[i],
        attrValue = instance.input.getAttribute("data-" + i.toLowerCase());

    if (typeof initial === "number") {
      instance[i] = parseInt(attrValue);
    }
    else if (initial === false) { // Boolean options must be false by default anyway
      instance[i] = attrValue !== null;
    }
    else if (initial instanceof Function) {
      instance[i] = null;
    }
    else {
      instance[i] = attrValue;
    }

    if (!instance[i] && instance[i] !== 0) {
      instance[i] = (i in o)? o[i] : initial;
    }
  }
}

// Helpers

var slice = Array.prototype.slice;

function $(expr, con) {
  return typeof expr === "string"? (con || document).querySelector(expr) : expr || null;
}

function $$(expr, con) {
  return slice.call((con || document).querySelectorAll(expr));
}

$.create = function(tag, o) {
  var element = document.createElement(tag);

  for (var i in o) {
    var val = o[i];

    if (i === "inside") {
      $(val).appendChild(element);
    }
    else if (i === "around") {
      var ref = $(val);
      ref.parentNode.insertBefore(element, ref);
      element.appendChild(ref);
    }
    else if (i in element) {
      element[i] = val;
    }
    else {
      element.setAttribute(i, val);
    }
  }

  return element;
};

$.bind = function(element, o) {
  if (element) {
    for (var event in o) {
      var callback = o[event];

      event.split(/\s+/).forEach(function (event) {
        element.addEventListener(event, callback);
      });
    }
  }
};

$.fire = function(target, type, properties) {
  var evt = document.createEvent("HTMLEvents");

  evt.initEvent(type, true, true );

  for (var j in properties) {
    evt[j] = properties[j];
  }

  return target.dispatchEvent(evt);
};

$.regExpEscape = function (s) {
  return s.replace(/[-\\^$*+?.()|[\]{}]/g, "\\$&");
};

$.siblingIndex = function (el) {
  /* eslint-disable no-cond-assign */
  for (var i = 0; el = el.previousElementSibling; i++);
  return i;
};

// Initialization

function init() {
  $$("input.awesomplete").forEach(function (input) {
    new _(input);
  });
}

// Are we in a browser? Check for Document constructor
if (typeof Document !== "undefined") {
  // DOM already loaded?
  if (document.readyState !== "loading") {
    init();
  }
  else {
    // Wait for it
    document.addEventListener("DOMContentLoaded", init);
  }
}

_.$ = $;
_.$$ = $$;

// Make sure to export Awesomplete on self when in a browser
if (typeof self !== "undefined") {
  self.Awesomplete = _;
}

// Expose Awesomplete as a CJS module
if (typeof module === "object" && module.exports) {
  module.exports = _;
}

return _;

}());

var assemble = (function() {
  "use strict";

  var key = 'AIzaSyA0RmX6KHUGbE8MPK5x9mVV1HCTul0uziw';

  return {
    init: function() {

      assemble.map.init( 'assemble.map.loaded' );
      assemble.events.init( 'assemble.events.loaded' );
      assemble.fields();

    },

    map: {

      mapElement: '',

      init: function( callback ) {

        assemble.map.mapElement = document.getElementById( 'map' );
        if ( assemble.map.mapElement && assemble.map.mapElement.getAttribute('data-lat') ) {
          var mapScript = document.createElement( 'script' );
          mapScript.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&callback=' + callback + '&key=' + key;
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
              var request = new XMLHttpRequest();
              request.addEventListener('load', assemble.events.find);
              request.open('POST', 'https://maps.googleapis.com/maps/api/geocode/json?key=' + key + '&address=' + findFormLocation.value );
              request.send();
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

    fields: function() {

      var inputs = document.querySelectorAll('input, textarea, select');
      if (inputs) {
        for (var i = inputs.length - 1; i >= 0; i--) {
          inputs[i].addEventListener('blur', function() {
            assemble.helpers.addClass(this, 'focused');
            this.checkValidity();
          }, false);
        };
      }

      if ('geolocation' in navigator) {
        var location = document.querySelectorAll('[name=location]');
        
        for (var i = location.length - 1; i >= 0; i--) {
          (function(i) {
            var locationButton = document.createElement('button');
            locationButton.innerHTML = 'Find';
            assemble.helpers.addClass(locationButton, 'find-address');
            locationButton.addEventListener('click', function(event) {
              
              event.preventDefault();
              assemble.helpers.addClass(locationButton, 'loading')
              navigator.geolocation.getCurrentPosition(function(position) {

                var latlng = document.querySelector('[name=latlng]');
                if ( latlng ) {
                  latlng.value = position.coords.latitude + ',' + position.coords.longitude;
                }
                
                var request = new XMLHttpRequest();
                request.addEventListener('load', function() {
                  var response = JSON.parse( this.responseText );
                  if ( response.results[0] && response.results[0].address_components[2] ) {
                    location[i].value = response.results[0].address_components[2].long_name;
                  }
                });
                request.open('GET', 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + position.coords.latitude + ',' + position.coords.longitude + '&sensor=true' );
                request.send();

              });
            }, false);
            location[i].parentNode.appendChild(locationButton);

            // location[i].addEventListener('keydown', function(event) {
            var keyThrottle = assemble.helpers.throttle(function() {
              var input = location[i];
              if (input.value && input.value !== undefined && input.value.length > 2) {
                var request = new XMLHttpRequest();
                request.addEventListener('load', function() {
                  var response = JSON.parse( this.responseText );
                  if ( response.status === 'OK' && response.results ) {
                    var autocompleteResults = [];
                    for (var i = 0; i < response.results.length; i++) {
                      autocompleteResults.push( { label: response.results[i].formatted_address, value: response.results[i].formatted_address + '//' + response.results[i].geometry.location.lat + ',' + response.results[i].geometry.location.lng } );
                    }
                    locationAutocomplete.list = autocompleteResults;
                    locationAutocomplete.evaluate();
                  }
                });
                request.open('POST', 'https://maps.googleapis.com/maps/api/geocode/json?key=' + key + '&address=' + input.value );
                request.send();
              }
            }, 1000);
            location[i].addEventListener( 'keypress', keyThrottle, false );
            var locationAutocomplete = new Awesomplete(location[i]);
            location[i].addEventListener('awesomplete-select', function(event) {
              var text = event.text.value;
              var text = text.split('//');
              event.text.value = text[0];
              var latlng = document.querySelector('[name=latlng]');
              if ( latlng ) {
                latlng.value = text[1];
              }

            });
            // }, false);

            /*location[i].addEventListener('blur', function() {
              if (this.value) {
                var request = new XMLHttpRequest();
                request.addEventListener('load', function() {
                  var response = JSON.parse( this.responseText );
                  var latlng = document.querySelector('[name=latlng]');
                  if ( response.status === 'OK' ) {
                    var primaryResult = response.results[ 0 ];
                    latlng.value = primaryResult.geometry.location.lat + ',' + primaryResult.geometry.location.lng;
                  }
                });
                request.open('POST', 'https://maps.googleapis.com/maps/api/geocode/json?key=' + key + '&address=' + this.value );
                request.send();
              }
            }, false);
            */
          })(i);
        };
      }



      var addAnother = document.querySelectorAll('.js-add-another');
      for (var i = addAnother.length - 1; i >= 0; i--) {
        addAnother[i].addEventListener('click', function(e) {
          e.preventDefault();
          var newInput = document.createElement('input');
          newInput.setAttribute('type', this.previousElementSibling.getAttribute('type'));
          newInput.setAttribute('name', this.previousElementSibling.getAttribute('name'));
          this.parentNode.insertBefore(newInput, this);
          newInput.focus();
        }, false);
      };



      var start = document.querySelector('[name="start"][type="datetime-local"]');
      var end = document.querySelector('[name="end"][type="datetime-local"]');
      if (start && end) {
        start.addEventListener('blur', function() {
          if ( this.value ) {
            this.nextElementSibling.innerHTML = '';
            var date = Math.round(new Date(this.value).getTime());
            var now = Date.now();
            if ( now > date ) {
              var error = 'Please select a date in the future';
              this.setCustomValidity(error);
              this.nextElementSibling.innerHTML = error;
            } else {
              this.setCustomValidity('');
              this.nextElementSibling.innerHTML = '';
            }
          } else {
            this.nextElementSibling.innerHTML = 'This field is required';
          }
        }, false);

        end.addEventListener('blur', function() {
          if ( this.value ) {
            this.nextElementSibling.innerHTML = '';
            var date = Math.round(new Date(this.value).getTime());
            var now = Date.now();
            if ( ! start.value ) {
              return;
            }
            var startDate = Math.round(new Date(start.value).getTime());

            if ( now > date || startDate > date ) {
              var error = 'Please select a date in the future';
              this.setCustomValidity(error);
              this.nextElementSibling.innerHTML = error;
            } else {
              this.setCustomValidity('');
              this.nextElementSibling.innerHTML = '';
            }
          } else {
            this.nextElementSibling.innerHTML = 'This field is required';
          }
        }, false);
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