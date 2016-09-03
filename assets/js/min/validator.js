function Validator(args) {

  if ( ! args ) {
    return;
  }
  
  this.element = args.element;
  this.rules = args.rules;
  this.status = '';

  this.validate = function() {

    this.status = '';
    for (var i = this.rules.length - 1; i >= 0; i--) {
      
      if ( this.rules[i] === 'required' ) {
        if ( this.element.value === '' ) {
          var label = this.element.parentNode.children[0];
          if (label && label.tagName === 'LABEL') {
            this.status = label.innerHTML + ' is required';
          } else {
            this.status = 'This field is required';
          }
        }
      }
    };

    this.element.setCustomValidity( this.status );
  }

};

