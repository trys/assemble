<?php

class EventModel
{
	
	function __construct( $args = array() )
	{
		$this->id = check_entity($args, 'id');
		$this->name = check_entity($args, 'name');
		$this->location = check_entity($args, 'location');
		$this->latlng = check_entity($args, 'latlng');
		$this->start = check_entity($args, 'start');
		$this->end = check_entity($args, 'end');
		$this->user_id = check_entity($args, 'user_id');

		if ( ! $this->user_id && $current_user = is_user_logged_in() ) {
			$this->user_id = $current_user->id;
		}
	}

	public $id = '';
	public $name = '';
	public $location = '';
	public $latlng = '';
	public $start = '';
	public $end = '';
	public $user_id = '';

	public function validate_creation()
	{
		if ( ! $this->name ) {
			$this->errors[ 'name' ] = 'Please provide the event name';
		}

		if ( ! $this->location ) {
			$this->errors[ 'location' ] = 'Please provide the event location';
		}

		if ( ! $this->start ) {
			$this->errors[ 'start' ] = 'Please provide the event start time';
		} else {
			if ( $start_time = strtotime( $this->start ) ) {
				$this->start = $start_time;
			} else {
				$this->errors[ 'start' ] = 'Please provide the start time in a valid format';
			}
		}

		if ( ! $this->end ) {
			$this->errors[ 'end' ] = 'Please provide the event end time';
		} else {
			if ( $end_time = strtotime( $this->end ) ) {
				$this->end = $end_time;
			} else {
				$this->errors[ 'end' ] = 'Please provide the end time in a valid format';
			}
		}


		if ( ! $this->errors && ! $this->user_id ) {
			redirect( 'register' );
		}

		return $this->errors ? $this->errors : false;
	}

	public function save()
	{
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

		$eventToSave = array(
			'name' => $this->name,
			'location' => $this->location,
			'latlng' => $this->latlng,
			'start' => $this->start,
			'end' => $this->end,
			'user_id' => $this->user_id,
		);

		$eventToSave = $firebase->push( DEFAULT_PATH . '/events', $eventToSave );

		return $eventToSave;
	}
	
}