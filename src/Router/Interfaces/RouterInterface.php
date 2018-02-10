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

namespace Kit\Http\Router\Interfaces;

use Closure;
use Kit\Http\Router\Repository;

interface RouterInterface
{

	/**
	* Registers a route with GET http method.
	*
	* @param 	$route <String
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function get($route=null, $callback=null, $prefix=array()) : Repository;

	/**
	* Registers a route with POST http method.
	*
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function post($route=null, $callback=null, $prefix=array()) : Repository;

	/**
	* Registers a route with PUT http method.
	*
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function put($route=null, $callback=null, $prefix=array()) : Repository;

	/**
	* Registers a route with DELETE http method.
	*
	* @param 	$route <String>
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function delete($route=null, $callback=null, $prefix=array()) : Repository;

	/**
	* Registers a route with any http method.
	*
	* @param 	$route <String
	* @param 	$callback <Mixed>
	* @param 	$prefix <Array>
	* @access 	public
	* @return 	void
	*/
	public function default($route=null, $callback=null, $prefix=array()) : Repository;

	/**
	* Checks if a route is registered.
	*
	* @param 	$route <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function hasRoute($route=''); 

	/**
	* @access 	public
	* @return 	String
	*/
	public function getTempRoute();

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function getTempCallback();

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getConfiguredRoute() : Array;

	/**
	* @access 	public
	* @return 	void
	*/
	public function attachMiddleWare();

	/**
	* @param 	$toString <Boolean>
	* @access 	public
	* @return 	String|Array
	*/
	public function getRequestUri($toString=false);

	/**
	* @param 	$file <String>
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function config($file=null, $key=null);

	/**
	* This method is used to set fallback if a parameter/slug validation fails. This method
	* accepts a closure as an argument.
	*
	* @param 	$fallbackClosure Closure
	* @access 	public
	* @return 	Object
	*/
	public function setValidatorFallback(Closure $fallbackClosure) : Repository;

	/**
	* Gives the created route a name.
	*
	* @param 	$name <String>
	* @access 	public
	* @return 	Object Http\Router\Repository
	*/
	public function alias(String $name='') : Repository;

	/**
	* Returns route method that is currently accessed.
	*
	* @access 	public
	* @return 	String
	*/
	public function getSharedRouteMethod();

	/**
	* Run the router.
	*
	* @access 	public
	* @return 	void
	*/
	public function run();

}