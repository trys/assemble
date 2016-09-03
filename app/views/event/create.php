<h1>Create an event</h1>
<form method="POST" class="primary">

	<?php if ( $errors = check_object( $viewmodel, 'errors' ) ) :?>
		<ul class="errors">
			<?php foreach ( $errors as $error ) :?>
				<li><?php echo $error;?></li>
			<?php endforeach;?>
		</ul>
	<?php endif;?>

	<?php text_input( 'name', 'Event Name', check_array( $_POST, 'name' ) ?: check_array( $_GET, 'event_name' ), true );?>
	<?php text_input( 'location', 'Location', check_array( $_POST, 'location' ) ?: check_array( $_GET, 'location' ), true );?>
	<?php hidden_input( 'latlng', check_array( $_POST, 'latlng' ) ?: check_array( $_GET, 'latlng' ) );?>
	<?php
	$start = check_array( $_POST, 'start' );
	if ( ! $start ) {
		$start = check_array( $_GET, 'start' );
		if ( $start ) {
			$start = date( 'Y-m-d\TH:i:s', esc( $start ) );
		}
	}
	text_input( 'start', 'Event Start', $start, true, array( 'type' => 'datetime-local', 'placeholder' => 'dd/mm/yyyy, hh:mm' ) );?>
	<?php
		$start = check_array( $_POST, 'end' );
		if ( ! $end ) {
			$end = check_array( $_GET, 'end' );
			if ( $end ) {
				$end = date( 'Y-m-d\TH:i:s', esc( $end ) );
			}
		}
		text_input( 'end', 'Event End', $end, true, array( 'type' => 'datetime-local', 'placeholder' => 'dd/mm/yyyy, hh:mm' ) );?>
	<input type="submit" name="submit" value="Go!" class="button" />
</form>

<script src="/assets/js/min/validator.js"></script>
<script>
	var form = document.forms[0];
	var validators = [
		new Validator( { element: form.name, rules: [ 'required' ] } ),
		new Validator( { element: form.location, rules: [ 'required' ] } ),
		new Validator( { element: form.start, rules: [ 'required' ] } ),
		new Validator( { element: form.end, rules: [ 'required' ] } )
	];

	for (var i = validators.length - 1; i >= 0; i--) {
		(function(i){
			validators[i].element.addEventListener('blur', function() {
				validators[i].validate();
			}, false);
		})(i);
	}
	
	form.submit.onclick = function() {
		assemble.helpers.addClass(form, 'submitted');
		for (var i = validators.length - 1; i >= 0; i--) {
			validators[i].validate();
		}
	}
</script>