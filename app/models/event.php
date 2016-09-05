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
		$this->long_desc = check_entity($args, 'long_desc');
		$this->short_desc = check_entity($args, 'short_desc');
		$this->host = check_entity($args, 'host');
		$this->guestlist = check_entity($args, 'guestlist');
		$this->tags = check_entity($args, 'tags');

		if ( ! $this->user_id && $current_user = is_user_logged_in() ) {
			$this->user_id = $current_user->id;
		}

		if ( check_entity($args, 'distance') ) {
			$this->distance = check_entity($args, 'distance');
		}
	}

	public $id = '';
	public $name = '';
	public $location = '';
	public $latlng = '';
	public $start = '';
	public $end = '';
	public $user_id = '';
	public $long_desc = '';
	public $short_desc = '';
	public $host = '';
	public $guestlist = '';
	public $tags = '';

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

		if ( $this->tags && ! is_array( $this->tags ) ) {
			$this->errors[ 'tags' ] = 'Please provide a list of tags';
		}

		if ( $this->guests && ! is_array( $this->guests ) ) {
			$this->errors[ 'guestlist' ] = 'Please provide a list of guests';
		}

		if ( ! $this->errors && ! $this->user_id ) {
			$args = array(
				'event_name' => $this->name,
				'location' => $this->location,
				'latlng' => $this->latlng,
				'start' => $this->start,
				'end' => $this->end,
				'long_desc' => $this->long_desc,
				'short_desc' => $this->short_desc,
				'host' => $this->host,
				'guestlist' => $this->guestlist,
				'tags' => $this->tags
			);
			redirect( 'register?' . http_build_query( $args ) );
		}

		return $this->errors ? $this->errors : false;
	}

	public function can_be_edited()
	{
		return $this->user_id && current_user_id() === $this->user_id;
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

		$eventToSave = array_map('sanitize', $eventToSave);

		$eventToSave = $firebase->push( DEFAULT_PATH . '/events', $eventToSave );

		return $eventToSave;
	}

	public function update()
	{
		$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);

		$eventToSave = array(
			'name' => $this->name,
			'location' => $this->location,
			'latlng' => $this->latlng,
			'start' => $this->start,
			'end' => $this->end,
			'user_id' => $this->user_id,
			'long_desc' => $this->long_desc,
			'short_desc' => $this->short_desc,
			'host' => $this->host,
			'guestlist' => $this->guestlist,
			'tags' => $this->tags
		);

		$eventToSave = array_map('sanitize', $eventToSave);

		$eventToSave = $firebase->update( DEFAULT_PATH . '/events/' . $this->id, $eventToSave );

		return $eventToSave;
	}

	
}