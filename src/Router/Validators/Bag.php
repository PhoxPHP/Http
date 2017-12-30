<?php
namespace Kit\Http\Router\Validators;

use CLosure;
use RuntimeException;
use Kit\Http\Router\Factory;

class Bag
{

	/**
	* @var 		$requestCriteriaList
	* @access 	private
	*/
	private 	$requestCriteriaList = ['get', 'post', 'delete', 'put', 'all'];

	/**
	* @var 		$requestCriteria
	* @access 	private
	*/
	private static $validatorsFallback = [];

	/**
	* @var 		$criteria
	* @access 	private
	*/
	private 	$criteria;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct() {}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getCriteriaList() : Array
	{
		return $this->requestCriteriaList;
	}

	/**
	* @param 	$criteria <String>
	* @access 	public
	*/
	public function from($criteria='') : Bag
	{
		if (!in_array($criteria, $this->requestCriteriaList)) {
		
			throw new RuntimeException(sprintf('%s is not a valid shared route method', $criteria));
		
		}

		$this->criteria = $criteria;
		return $this;
	}

	/**
	* @param 	$route 	<String>
	* @access 	public
	* @return 	void
	*/
	public function getClosure($route='')
	{
		return Bag::$validatorsFallback[$this->criteria][$route] ?? null;
	}

	/**
	* Adds a route's validator fallback to list of fallbacks.
	*
	* @param 	$fallbackClosure Closure
	* @param 	$factory Http\Router\Factory
	* @access 	public
	* @return 	Object
	*/
	public function pushRouteFallback(Factory $factory, Closure $fallbackClosure) : Factory
	{
		$sharedMethod = $factory->getSharedRouteMethod();

		if (!in_array($sharedMethod, $this->requestCriteriaList)) {
		
			throw new RuntimeException(sprintf('%s is not a valid shared route method', $sharedMethod));
		
		}

		Bag::$validatorsFallback[$sharedMethod][$factory->getTempRoute()][] = $fallbackClosure;
		
		return $factory;
	}

}