<?php
/**
* MIT License
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:

* The above copyright notice and this permission notice shall be included in all
* copies or substantial portions of the Software.

* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

/**
* @author 	Peter Taiwo
*/

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