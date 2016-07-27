<?php

function p( $request ) {
	echo '<pre style="clear: both; background: #F2F2F2; padding: 0.5rem;">' . print_r( $request, true ) . '</pre>';
}

function check_array( $array, $key, $return = '' ) {
	return ! empty( $array[ $key ] ) ? $array[ $key ] : $return;
}

function check_object( $object, $key, $return = '' ) {
	return ! empty( $object->$key ) ? $object->$key : $return;
}

function check_entity( $entity, $key, $return = '' ) {
	return is_array( $entity ) ? check_array( $entity, $key, $return ) : check_object( $entity, $key, $return );
}

function esc( $str ) {
	return htmlentities( $str );
}

function sanitize( $str ) {
	$search = array(
		'@<script[^>]*?>.*?</script>@si',
		'@<[\/\!]*?[^<>]*?>@si',
		'@<style[^>]*?>.*?</style>@siU',
		'@<![\s\S]*?--[ \t\n\r]*>@'
	);

	return trim( strip_tags( preg_replace( $search, '', $str ) ) );
}

function redirect( $controller = '', $action = '', $parameters = '', $with_front = false ) {
	header( 'Location: ' . url( $controller, $action, $parameters, true ), '302' );
	exit;
}

function url( $controller = '', $action = '', $parameters = '', $with_front = false ) {

	$location = '';

	if ( $with_front ) {
		$location = ( $url = check_array( $_SERVER, 'HTTP_HOST' ) ) ? $url : $_SERVER['SERVER_NAME'];

		if ( ! isset( $_SERVER[ 'HTTPS' ] ) || $_SERVER[ 'HTTPS' ] !== 'on' ) {
			$location = 'http://' . $location;
		} else {
			$location = 'https://' . $location;
		}
	}

	if ( $controller ) {
		$location .= '/' . $controller;
	}

	if ( $action ) {
		$location .= '/' . $action;
	}

	if ( is_array( $parameters ) ) {
		foreach ( $parameters as $parameter ) {
			$location .= '/' . $parameter;
		}
	} elseif ( $parameters ) {
		$location .= '/' . $parameters;
	}

	return $location;

}

function order_object($array = array(), $key = 'points') {
	usort($array, function($a, $b) use ($key) {
		$p1 = check_object( $b, $key, 0 );
		$p2 = check_object( $a, $key, 0 );
		return $p1 - $p2;
	});
	return $array;
}


function input_attribute( $attribute, $value = false ) {
	return $value === false ? '' : ' ' . $attribute . '="' . $value . '"';
}


function fields_exist( $array = array(), $values = array(), $empty = true ) {
	$good = true;
	foreach ( $values as $v ) {
		if ( ( $empty && empty( $array[ $v ] ) ) || ! isset( $array[ $v ] ) ) {
			$good = false;
			break;
		}
	}
	return $good;
}


function text_input( $name, $label, $value = '', $required = false, $attributes = array() ) {

	if ( ! $attributes ) {
		$attributes = array(
			'type' => 'text'
		);
	}

	$attributes_string = '';
	foreach( $attributes as $k => $v ) {
		$attributes_string .= $k . '="' . $v . '" ';
	}

	echo '<p>
	<label for="' . $name . '">' . $label . '</label>
	<input '
	. $attributes_string
	. input_attribute( 'id', $name )
	. input_attribute( 'name', $name )
	. input_attribute( 'value', esc( $value ) )
	. input_attribute( 'required', $required )
	. '/></p>';
}


function number_input( $name, $label, $value = '', $required = false, $min = 0, $max = 50 ) {
	echo '<p>
	<label for="' . $name . '">' . $label . '</label>
	<input type="number"'
	. input_attribute( 'id', $name )
	. input_attribute( 'name', $name )
	. input_attribute( 'value', esc( $value ) )
	. input_attribute( 'required', $required )
	. input_attribute( 'min', $min )
	. input_attribute( 'max', $max )
	. input_attribute( 'step', 1 )
	. '/></p>';
}


function hidden_input( $name, $value = '' ) {
	echo '
	<input type="hidden"'
	. input_attribute( 'id', $name )
	. input_attribute( 'name', $name )
	. input_attribute( 'value', esc( $value ) )
	. '/>';
}


function select_input( $name, $label, $fields = array(), $value = '', $none = '', $required = false, $field_key = '_id', $field_label = 'name' ) {

	echo '<p>
	<label for="' . $name . '">' . $label . '</label>
	<select'
	. input_attribute( 'id', $name )
	. input_attribute( 'name', $name )
	. input_attribute( 'required', $required )
	. '>';

	if ( $none ) {
		echo '<option value="">' . $none . '</option>';
	}

	if ( $fields ) {
		foreach ( $fields as $field ) {
			$selected = $value == check_object( $field, $field_key ) ? ' selected' : '';
			echo '<option value="' . check_object( $field, $field_key ) . '"' . $selected . '>' . check_object( $field, $field_label ) . '</option>';
		}
	}

	echo '</select></p>';
}


function is_user_logged_in() {
	return check_array( $_SESSION, 'user', false );
}


function current_user_id() {
	$current_user = is_user_logged_in();
	return $current_user ? $current_user->id : '';
}


function format_date($date, $echo = true) {
	$minutes = date( 'i', $date );
	$date = date( 'jS F', $date ) . ' at ' . date( 'g', $date ) . ( $minutes === '00' ? '' : '.' . $minutes ) . date( 'a', $date );

	if ( $echo ) {
		echo $date;
	} else {
		return $date;
	}
}