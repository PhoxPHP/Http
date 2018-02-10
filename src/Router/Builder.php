<?php
/**
* @author 	Peter Taiwo
* @version 	1.0.0
*
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

namespace Kit\Http\Router;

use StdClass;
use RuntimeException;
use Kit\Http\Router\{Repository, Bag, QueryStringConnector};

class Builder
{

	/**
	* @var 		$repository
	* @access 	private
	*/
	private 	$repository;

	/**
	* @var 		$routerBag
	* @access 	private
	*/
	private 	$routerBag;

	/**
	* @var 		$routeLabel
	* @access 	public
	*/
	public 		$routeLabel;

	/**
	* @var 		$callbackTypes
	* @access 	private
	*/
	private 	$callbackTypes = array('object', 'string');

	/**
	* @var 		$buildObject
	* @access 	private
	*/
	private 	$buildObject;

	/**
	* @var 		$tempRoute
	* @access 	private
	*/
	private 	$tempRoute;

	/**
	* @var 		$tempMethod
	* @access 	private
	*/
	private 	$tempMethod;

	/**
	* @var 		$tempCallback
	* @access 	private
	*/
	private 	$tempCallback;

	/**
	* @var 		$tempParameters
	* @access 	private
	*/
	private 	$tempParameters = array();

	/**
	* @var 		$tempValidator
	* @access 	private
	*/
	private 	$tempValidator;

	/**
	* @var 		$sharedMethod
	* @access 	private
	*/
	private 	$sharedMethod;

	/**
	* @var 		$__build
	* @access 	public
	*/
	public 		$__build = false;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
		$this->routerBag = new Bag($repository);
	}

	/**
	* Builds registered routes.
	*
	* @see 		Http\Router\Bag
	* @param 	$repository Http\Router\Repository
	* @access 	public
	* @return 	Object Http\Router\Bag
	*/
	public function buildRoute(Repository $repository)
	{
		$routes = (Object) Bag::getRoutes();
		$routes = $routes->all;

		foreach(array_keys($routes) as $route) {

			if (isset($routes[$route]['callback'])) {
				$routeArray = array();
				$routeCallback = $routes[$route]['callback'];
				$routeParameterValidator = $routes[$route]['validator'];
				$requestUriArray = $repository->getRequestUri();
				$requestUri = $repository->getRequestUri(true);

				$this->routeLabel = $route;

				if(!in_array(gettype($routeCallback), $this->callbackTypes)) {
				
					throw new RuntimeException(sprintf("Unknown error occured."));
				
				}

				foreach(explode("/", $route) as $i) {

					if(!empty($i)) {
					
						if (QueryStringConnector::isQueued("/$i") && (Integer) QueryStringConnector::getValidationFor("/$i") == 0) {
					
							$i = $this->resolveRouteQueryString("/$i");
					
						}

						$routeArray[] = $i;
					}
				}

				if (QueryStringConnector::isQueued("$route") && QueryStringConnector::getValidationFor("$route") == 0 && !empty($requestUriArray)) {

					$requestUriArray[0] = "/".$this->resolveRouteQueryString($requestUriArray[0]);
				
				}

				if (empty($requestUriArray)) {

					$requestUriArray = ['/'];
				
				}

				if (empty($routeArray)) {

					$routeArray = ['/'];
				
				}

				// The uri will only be parsed if the size of requestUri is the same with the route count...
				if(sizeof($requestUriArray) == sizeof($routeArray)) {

					$routeObject = $this->resolveRoute($routeArray);
					
					$this->tempRoute = $routeObject->uri;
					
					$this->tempCallback = $routeCallback;
					
					$this->sharedMethod = $routes[$route]['shared_method'];

					if (QueryStringConnector::isQueued("$route") && QueryStringConnector::getValidationFor("$route") == 0) {

						$requestUri = "/".$this->resolveRouteQueryString($requestUri);
					
					}

					$routeUrl = implode("/", $routeArray);

					if (strlen($routeUrl) > 0 && $routeUrl[0] != '/') {
					
						$routeUrl = '/'.$routeUrl;
					
					}

					if (strlen($requestUri) > 0 && $requestUri[0] != '/') {

						$requestUri = '/'.$requestUri;
					
					}

					// Setting this for empty routes..
					if ($requestUri == '') {

						$requestUri = '/';
					
					}

					if (empty($routeObject->slugs) && $requestUri == $routeUrl) {

						$this->tempValidator = $routeParameterValidator;
						$this->routerBag->pushMatchedRoute($this);
						
						$this->__build = true;
					}else{

						if ($routeObject->slugs) {
							
							$parametersToPush = array();
							$uriObject = $this->resolveRoute($requestUriArray);
							$requestUriParameters = $uriObject->split;
							$requestUriParametersArray = $requestUriParameters; 
							$routeSegments = $routeObject->split;
							$routeSegmentsLabel = $routeObject->split;
							$routeParameters = array_flip($routeObject->slugs);
							$routeParameterLabel = array_values($routeObject->slugs);

							/**
							* We will be retrieving our route parameters by getting the offset
							* of the router uri from @var $requestUriParameters.
							*/
							
							$uriSegments = $uriObject->split;
							$uriSegmentsSwap = array_flip($uriSegments);
							$parameterKeysArray = array_flip(array_keys($routeObject->slugs));

							foreach(array_values($routeSegments) as $iterator => $key) {

								$offset = $iterator;
								
								if (in_array($key, $routeParameterLabel)) {

									$parametersToPush[$routeParameters[$key]] = $this->resolveParameter($uriSegments[$offset]);
									/**
									* If the route contains parameters, we will check if the normal segments registered
									* in the routes is the same with normal segments returned from the request uri. We will have
									* to unset the parameters from the route segments so we would be left with just the normal
									* segments.
									*/
									unset($requestUriParametersArray[$offset]);
									unset($routeSegmentsLabel[$offset]);
								}

							}

							/**
							* If @var $requestUriParametersArray is not empty, we will check both routes by comparing
							* both route string..
							*/
							$this->tempValidator = $routeParameterValidator;

							$this->routerBag->pushMatchedRoute($this, $parametersToPush);
							
							if (empty($requestUriParametersArray)) {
							
								$this->__build = true;
							
							}else{

								if (implode(".", $requestUriParametersArray) == implode(".", $routeSegmentsLabel)) {
								
									return $this->__build = true;
								
								}else{
								
									$this->__build = false;
								
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	* Returns the matched route.
	*
	* @see 		Http\Router\Builder::buildRoute()
	* @access 	public
	* @return 	String
	*/
	public function getRoute()
	{
		return $this->tempRoute;
	}

	/**
	* Returns the callback provided for the current route. 
	*
	* @see 		Http\Router\Builder::buildRoute()
	* @access 	public
	* @return 	Mixed
	*/
	public function getCallback()
	{
		return $this->tempCallback;
	}

	/**
	* Returns parameters in a given url.
	*
	* @access 	public
	* @return 	Array
	*/
	public function getParameters()
	{
		return $this->tempParameters;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getValidator()
	{
		return $this->tempValidator;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getMethod()
	{
		return $this->sharedMethod;
	}

	/**
	* Updates a registered route by removing the route's request query string.
	*
	* @param 	$route <String>
	* @access 	private
	* @return 	String
	*/
	private function resolveRouteQueryString($route)
	{
		$route = explode('?', $route);
		return $route[0];
	}

	/**
	* Removes/Strips off url query string from given string.
	* Example: /profile/user?id=2 -----> /profile/user/
	*
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function resolveParameter($string='')
	{
		if (!$stapler = strpos($string, '?')) {

			return $string;
		
		}

		$substring = substr($string, $stapler);

		return str_replace($substring, '', $string);
	}

	/**
	* Checks if a string contains parameters or not.
	*
	* @param 	$routeArray <Array>
	* @param 	$prefix <String>
	* @access 	private
	* @return 	Object
	*/
	private function resolveRoute(array $routeArray=[], $prefix="") : StdClass
	{
		$response = new StdClass();
		// Spliting url segments into array....
		$preg = preg_split("/[\s,]+/", implode(", ", $routeArray));
		$response->split = $preg;
		$response->uri = $prefix . implode("/", $routeArray);
		$response->slugs = array();

		foreach($response->split as $segment) {
		
			if (preg_match("/:.*/", $segment, $match)) {
		
				$response->slugs[str_replace(':', '', $segment)] = $segment; 
		
			}
		}

		return $response;
	}

}