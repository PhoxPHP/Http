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
use Kit\Http\Router\Repository;

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
	* @param 	$repository Http\Router\Repository
	* @param 	$alias 	<String>
	* @access 	public
	* @return 	Boolean
	*/
	public function createNewALiasFromRepository(Repository $repository, $alias='')
	{
		$route = $repository->getTempRoute();

		if (strlen($alias) > 0 && !Alias::hasAlias($alias)) {
		
			Alias::$aliases[$alias] = $route;
		
			return true;
		
		}

		return false;
	}

	/**
	* Checks if a route with the name exists.
	*
	* @param 	$alias <String>
	* @access 	public
	* @return 	Boolean
	* @static
	*/
	public static function hasAlias(String $alias='')
	{
		$keyword = Alias::$aliases[$alias];
		return (isset($keyword)) ? true : false;
	}

	/**
	* Returns a route name.
	*
	* @param 	$alias <String>
	* @access 	public
	* @return 	Mixed
	* @static
	*/
	public static function getAlias(String $alias='')
	{
		return Alias::$aliases[$alias] ?? null;
	}

}