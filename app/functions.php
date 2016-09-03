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

function paragraph( $str ) {
	echo nl2p( esc( $str ) );
}

/**
 * Returns string with newline formatting converted into HTML paragraphs.
 *
 * @author Michael Tomasello <miketomasello@gmail.com>
 * @copyright Copyright (c) 2007, Michael Tomasello
 * @license http://www.opensource.org/licenses/bsd-license.html BSD License
 * 
 * @param string $string String to be formatted.
 * @param boolean $line_breaks When true, single-line line-breaks will be converted to HTML break tags.
 * @return string
 */
function nl2p($string, $line_breaks = true)
{
    // Remove existing HTML formatting to avoid double-wrapping things
    $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);
 
    // It is conceivable that people might still want single line-breaks
    // without breaking into a new paragraph.
    if ($line_breaks == true)
        return '<p>'.preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '<br><br>'), trim($string)).'</p>';
    else 
        return '<p>'.preg_replace("/([\n]{1,})/i", "</p>\n<p>", trim($string)).'</p>';
}

function sanitize( $input ) {
	if ( is_array( $input ) ) {
		$input = array_filter( $input );
		$input = array_values( $input );
		return array_map( 'sanitize', $input );
	} elseif ( is_string( $input ) || is_int( $input ) ) {

		$search = array(
			'@<script[^>]*?>.*?</script>@si',
			'@<[\/\!]*?[^<>]*?>@si',
			'@<style[^>]*?>.*?</style>@siU',
			'@<![\s\S]*?--[ \t\n\r]*>@'
		);

		return trim( strip_tags( preg_replace( $search, '', $input ) ) );

	} else {
		return $input;
	}
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

function order_object($array = array(), $key = 'start', $asc = true) {
	usort($array, function($a, $b) use ($key) {
		$p1 = check_object( $b, $key, 0 );
		$p2 = check_object( $a, $key, 0 );
		return $asc ? strcmp( $p1, $p2 ) : strcmp( $p2, $p1 );
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


function textarea_input( $name, $label, $value = '', $required = false, $attributes = array() ) {

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
	<textarea '
	. $attributes_string
	. input_attribute( 'id', $name )
	. input_attribute( 'name', $name )
	. input_attribute( 'required', $required )
	. '>' . esc( $value ) . '</textarea></p>';
}


function multiline_input( $name, $label = '', $value = array() ) {
	echo '<p class="multiline "><label>' . $label . '</label> ';
	$value = is_array( $value ) ? $value : array();
	foreach ( $value as $line ) {
		echo '<input type="text"'
		. input_attribute( 'name', $name )
		. input_attribute( 'value', esc( $line ) )
		. '/>';
	}

	if ( ! $value ) {
		echo '<input type="text"' . input_attribute( 'name', $name ) . '/>';
	}

	echo '<button class="button button--discreet js-add-another">Add Another</button>';
	echo '<noscript><input type="text"' . input_attribute( 'name', $name ) . '/></noscript>';
	
	echo '</p>';
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


function is_url($to_test) {
	preg_match('/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/', $to_test, $matches);
	return ( $matches );
}

function event_date( $event ) {
	$start = check_object( $event, 'start' );
	$end = check_object( $event, 'end' );

	echo '<time datetime="' . date( 'c', $start ) . '">' . format_date( $start, false ) . '</time>';

	if ( $start !== $end ) {
		echo ' - <time datetime="' . date( 'c', $end ) . '">';
		if ( date( 'Ymd', $start ) === date( 'Ymd', $end ) ) {
			$minutes = date( 'i', $end );
			echo date( 'g', $end ) . ( $minutes === '00' ? '' : '.' . $minutes ) . date( 'a', $end );
		} else {
			echo format_date( $end, false );
		}
		echo '</time>';
	}
}


function hold_event() {
	if ( ! empty( $_GET[ 'event_name' ] ) ) :?>
		<?php hidden_input( 'event_name', check_array( $_GET, 'event_name' ) );?>
		<?php hidden_input( 'location', check_array( $_GET, 'location' ) );?>
		<?php hidden_input( 'latlng', check_array( $_GET, 'latlng' ) );?>
		<?php hidden_input( 'start', check_array( $_GET, 'start' ) );?>
		<?php hidden_input( 'end', check_array( $_GET, 'end' ) );?>
	<?php endif;
}