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

namespace Kit\Http\Router;

use RuntimeException;
use Kit\Http\Router\Factory;

class Alias
{

	/**
	* @var 		$aliases
	* @access 	private
	*/
	private static $aliases=[];

	/**
	* @var 		$method
	* @access 	protected
	*/
	protected 	$method;

	/**
	* @param 	$method <String>
	* @access 	public
	* @return 	Object Http\Router\Alias
	*/
	public function setMethodCriteria($method='') : Alias
	{
		$this->method = $method;
		return $this;
	}

	/**
	* Creates an alias for a route. This can also be called naming a route.
	*
	* @param 	$factory Http\Router\Factory
	* @param 	$alias 	<String>
	* @access 	public
	* @return 	void
	*/
	public function createNewALiasFromFactory(Factory $factory, $alias='')
	{
		$route = $factory->getTempRoute();

		if (strlen($this->method) > 0) {
		
			Alias::$aliases[$this->method][$alias] = $route;
		
			return true;
		
		}

		Alias::$aliases[$alias] = $route;
	}

	/**
	* Checks if a route with the name exists. Sometimes a route can might be accessed with multiple
	* http requests methods. To check if the route is accessed with a certain method, the second
	* parameter can be used to specify the method.
	*
	* @param 	$alias <String>
	* @param 	$method <String>
	* @access 	public
	* @return 	Boolean
	* @static
	*/
	public static function hasAlias(String $alias='', $method='')
	{
		$keyword = Alias::$aliases[$alias];

		if ($method !== '') {
		
			if (!isset(Alias::$aliases[$method])) {
		
				return false;
		
			}
		
			$keyword = Alias::$aliases[$method][$alias];
		
		}
		
		return (isset($keyword)) ? true : false;
	}

}