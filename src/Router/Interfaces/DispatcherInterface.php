<?php
namespace Kit\Http\Router\Interfaces;

use StdClass;

interface DispatcherInterface
{

	/**
	* @param 	$callback <Object>
	* @access 	public
	* @return 	Mixed
	*/
	public function dispatch(StdClass $callback);

}