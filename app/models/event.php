<?php

class Event
{
	
	function __construct( $args = array() )
	{
		$this->title = check_array($args, 'title');
	}

	public $title = '';
	
}