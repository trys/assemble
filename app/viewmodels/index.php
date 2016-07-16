<?php

class ViewModel
{
	function __construct( $args = array() )
	{
		$this->title = check_array($args, 'title', 'assemble') . ' - assemble';
	}

	public $title = '';
}