<?php
namespace Kit\Http\Router\Exceptions;

use Exception;
use App\BaseException;

class RouteNotFoundException extends BaseException
{

	/**
	* @var 		$template
	* @access 	public
	*/
	public 		$template = '404x';

	/**
	* @var 		$code
	* @access 	protected
	*/
	protected 	$code = 404;

	/**
	* @param 	$message <String>
	* @access 	public
	* @return 	void
	*/
	public function __construct($message='')
	{
		parent::__construct($message);
	}

}