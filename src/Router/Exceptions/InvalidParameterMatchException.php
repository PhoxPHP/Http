<?php
namespace Kit\Http\Router\Exceptions;

use Exception;
use App\BaseException;
use App\Exceptions\Contract\ExceptionContract;

class InvalidParameterMatchException extends BaseException implements ExceptionContract
{

	/**
	* @var 		$code
	* @access 	protected
	*/
	protected 	$code = 404;

	/**
	* @var 		$message
	* @access 	protected
	*/
	protected 	$message;

	/**
	* @var 		$view
	* @access 	protected
	*/
	protected 	$view;

	/**
	* @param 	$options <Array>
	* @access 	public
	* @return 	void
	*/
	public function __construct(...$options)
	{
		$this->setCode(404);
		$this->setView('exception');
		$this->setMessage($options[0]);
		parent::__construct();
	}

	/**
	* {@inheritDoc}
	*/
	public function setCode(int $code)
	{
		$this->code = $code;
	}

	/**
	* {@inheritDoc}
	*/
	public function setMessage(String $message)
	{
		$this->message = $message;
	}

	/**
	* {@inheritDoc}
	*/
	public function setView(String $view)
	{
		$this->view = $view;
	}

	/**
	* {@inheritDoc}
	*/
	public function getExceptionCode() : int
	{
		return $this->code;
	}

	/**
	* {@inheritDoc}
	*/
	public function getExceptionMessage() : String
	{
		return $this->message;
	}

	/**
	* {@inheritDoc}
	*/
	public function getView() : String
	{
		return $this->view;
	}

}