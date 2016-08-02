<?php

class ViewModel
{
	function __construct( $args = array() )
	{
		$this->title = check_array($args, 'title', 'assemble') . ' - assemble';
		$this->event = check_array($args, 'event');
		$this->user = check_array($args, 'user');
	}

	public $title = '';
	public $event = '';
	public $user = '';
}