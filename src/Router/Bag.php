<?php
/**
* @author 		Peter Taiwo <peter@phoxphp.com>
* @package 		Kit\Http\Router\Bag
* @license 		MIT License
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

namespace Kit\Http\Router;

use StdClass;
use Kit\Http\Router\Builder;
use Kit\Http\Router\Contracts\RepositoryContract;

class Bag
{

	/**
	* @var 		$router
	* @access 	private
	*/
	private 	$router;

	/**
	* @var 		$registeredRoutes
	* @access 	private
	*/
	private 	$registeredRoutes = [];

	/**
	* @var 		$routes
	* @access 	private
	*/
	private static $routes;

	/**
	* @var 		$matchedRoute
	* @access 	private
	*/
	private static $matchedRoute = [];

	/**
	* @param 	$router Http\Router\Contracts\RepositoryContract
	* @access 	public
	* @return 	void
	*/
	public function __construct(RepositoryContract $router)
	{
		$this->router = $router;
		Bag::$routes = array('get' => array(), 'post' => array(), 'put' => array(), 'delete' => array(), 'all' => array());
	}

	/**
	* @param 	$router Http\Router\Contracts\RepositoryContract
	* @param 	$method <String>
	* @param 	$callback <Mixed>
	* @param 	$validator <Array>
	* @access 	public
	* @return 	void
	*/
	public function pushRoute(RepositoryContract $router, $method='', $callback='', array $validator=array())
	{
		$routeObject = new StdClass;

		Bag::$routes[$method][] = $router->getTempRoute();

		Bag::$routes['all'][$router->getTempRoute()] = [
			'callback' => $callback,
			'validator' => $validator,
			'shared_method' => $router->getSharedRouteMethod()
		];
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public static function getRoutes() : Array
	{
		return Bag::$routes;
	}

	/**
	* Pushes a matched route to Http\Router\Bag::$matchedRoute. Parameters are being
	* pushed using @param $parameters so as to avoid all parameters from being pushed
	* as well.
	*
	* @param 	$builder Http\Router\Builder
	* @param 	$parameters <Array>
	* @access 	public
	* @return 	void
	*/
	public function pushMatchedRoute(Builder $builder, array $parameters=[])
	{
		Bag::$matchedRoute = array('route' => $builder->getRoute(),
			'callback' => $builder->getCallback(),
			'uri' => $this->router->getRequestUri(true),
			'parameters' => $parameters,
			'validator' => $builder->getValidator(),
			'shared_method' => $builder->getMethod()
		);
	}

	/**
	* Returns the matched route.
	*
	* @access 	public
	* @return 	Array
	*/
	public static function getAccessedRoute() : Array
	{
		return Bag::$matchedRoute;
	}

}