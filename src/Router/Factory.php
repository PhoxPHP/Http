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

use Closure;
use App\Config;
use RuntimeException;
use Kit\DependencyInjection\Injector\InjectorBridge;
use Kit\Http\Router\Interfaces\{RouterInterface, Dispatchable};
use Kit\Http\Router\{Alias, Builder, Bag, Dispatcher, QueryStringConnector};
use Kit\Http\Router\Validators\{RouteParameterValidator, RouteCallbackTypeValidator, Bag as ValidatorsRepo};

class Factory implements RouterInterface, Dispatchable
{

	/**
	* @var 		$requestUri
	* @access 	private
	*/
	private 	$requestUri;

	/**
	* @var 		$requestMethod
	* @access 	private
	*/
	private 	$requestMethod;

	/**
	* @var 		$routerBag
	* @access 	private
	*/
	private 	$routerBag;

	/**
	* @var 		$routeBuilder
	* @access 	private
	*/
	private 	$routeBuilder;

	/**
	* @var 		$routeType
	* @access 	private
	*/
	private 	$routeType;

	/**
	* @var 		$filtered
	* @access 	private
	*/
	private 	$filtered;

	/**
	* @var 		$route
	* @access 	private
	*/
	private 	$route;

	/**
	* @var 		$routeCallback
	* @access 	private
	*/
	private 	$routeCallback;

	/**
	* @var 		$validators
	* @access 	private
	*/
	private 	$validators=[];

	/**
	* @var 		$routeValidator
	* @access 	private
	*/
	private 	$routeValidator=[];

	/**
	* @var 		$routeMethod
	* @access 	private
	*/
	private 	$routeMethod;

	########################
	# CONSTANTS
	########################
	const 		GET 	= 'get';
	const 		POST 	= 'post';
	const 		PUT 	= 'put';
	const 		DELETE 	= 'delete';
	const 		ALL 	= 'all';

	/**
	* We will be initializing our properties here...
	*
	* @access 	public
	* @return 	void
	*/
	public function __construct()
	{
		$this->routerBag = new Bag($this);
		$this->routeBuilder = new Builder($this);
		$this->requestUri = $_SERVER['REQUEST_URI'];
		$this->requestMethod = $_SERVER['REQUEST_METHOD'];
	}

	/**
	* {@inheritDOc}
	*/
	public function get($route=null, $callback=null, $validator=[]) : Factory
	{
		if ($this->requestMethod == 'GET') {
			$this->route = $route;
			$this->routeCallback = $callback;
			$this->routeValidator = $validator;
			$this->routerBag->pushRoute($this, $this->routeMethod = Factory::GET, $callback, $validator);
		}

		return $this;
	}

	/**
	* {@inheritDOc}
	*/
	public function post($route=null, $callback=null, $validator=[]) : Factory
	{
		if ($this->requestMethod == 'POST') {
			$this->route = $route;
			$this->routeCallback = $callback;
			$this->routeValidator = $validator;			
			$this->routerBag->pushRoute($this, $this->routeMethod = Factory::POST, $callback, $validator);
		}

		$this->route = null;
		return $this;
	}

	/**
	* {@inheritDOc}
	*/
	public function put($route=null, $callback=null, $validator=[]) : Factory
	{
		if ($this->requestMethod == 'PUT') {
			$this->route = $route;
			$this->routeCallback = $callback;
			$this->routeValidator = $validator;			
			$this->routerBag->pushRoute($this, $this->routeMethod = Factory::PUT, $callback, $validator);
		}

		$this->route = null;
		return $this;
	}

	/**
	* {@inheritDOc}
	*/
	public function delete($route=null, $callback=null, $validator=[]) : Factory
	{
		if ($this->requestMethod == 'DELETE') {
			$this->route = $route;
			$this->routeCallback = $callback;
			$this->routeValidator = $validator;			
			$this->routerBag->pushRoute($this, $this->routeMethod = Factory::DELETE, $callback, $validator);
		}

		$this->route = null;
		return $this;
	}

	/**
	* {@inheritDOc}
	*/
	public function default($route=null, $callback=null, $validator=[]) : Factory
	{
		$this->route = $route;
		$this->routeCallback = $callback;
		$this->routeValidator = $validator;		
		$this->routerBag->pushRoute($this, $this->routeMethod = Factory::ALL, $callback, $validator);
		$this->route = null;
		return $this;
	}

	/**
	* {@inheritDoc}
	*
	* @param 	$route <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function hasRoute($route='')
	{
		return (isset(Bag::getRoutes()['all'][$route])) ? true : false;
	}

	/**
	* Attaches registered middleware to router.
	*
	* @access 	public
	* @return 	void
	*/
	public function attachMiddleWare() {}

	/**
	* Returns the route that is passed to Http\Router\Builder.
	*
	* @access 	public
	* @return 	String
	*/
	public function getTempRoute()
	{
		return $this->route;
	}

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function getTempCallback()
	{
		return $this->routeCallback;
	}

	/**
	* @access 	public
	* @return 	Array
	*/
	public function getTempValidator()
	{
		return $this->routeValidator;
	}

	/**
	* This method returns the filtered request uri.
	* E.g: /profile/id/1/
	*
	* @param 	$toString <Boolean>
	* @access 	public
	* @return 	String|Array
	*/
	public function getRequestUri($toString=false)
	{
		$requestUri = explode("/", $this->requestUri);
		$requestUri = array_values(array_diff($requestUri, explode("/", $_SERVER['SCRIPT_NAME'])));

		if(true == $toString) {
	
			$requestUri = implode("/", $requestUri);
	
		}

		return $requestUri;
	}

	/**
	* If a route has query string parameters and you want to validate the uri query string
	* parameters with the registered route, all you need to do is chain this method and set the
	* @param $option to true.
	* This will tell the router to watch over this route and make sure it's query string
	* parameters matches with the uri's query string parameters if any is detected.
	*
	* Usage:
	* $router->get('/', 'hello@world')->secureRouteQueryString(true); 
	*
	* @param 	$option <Boolean>
	* @access 	public
	* @return 	Object
	*/
	public function secureRouteQueryString(Bool $option=false) : Factory
	{
		$option = (Integer) $option;
		$queryStringConnector = new QueryStringConnector($this);
		$queryStringConnector->setRuleFor($this->getTempRoute(), $option);
		return $this;
	}

	/**
	* {@inheritDoc}
	*
	* @access 	public
	* @return 	Array
	*/
	public function getConfiguredRoute() : Array
	{
		return Bag::getAccessedRoute();
	}

	/**
	* {@inheritDoc}
	*
	* @param 	$file <String>
	* @param 	$key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function config($file=null, $key=null)
	{
		return $this->get($file, $key);
	}

	/**
	* {@inheritDOc}
	*/
	public function getSharedRouteMethod()
	{
		return $this->routeMethod;
	}

	/**
	* {@inheritDoc}
	*
	* @param 	Closure $fallbackClosure
	* @access 	public
	* @return 	Object
	*/
	public function setValidatorFallback(Closure $fallbackClosure) : Factory
	{
		$validatorsRepo = new ValidatorsRepo();
		$validatorsRepo->pushRouteFallback($this, $fallbackClosure);
		return $this;
	}

	/**
	* @param 	$name <String>
	* @access 	public
	* @return 	Object Http\Router\Factory
	*/
	public function alias($name='') : Factory
	{
		if ($name == '') {

			throw new RuntimeException('Route alias cannot be empty');
		
		}

		$alias = new Alias();
		$alias->setMethodCriteria($this->getSharedRouteMethod())->createNewAliasFromFactory($this, $name);
		return $this;
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function run()
	{
		$this->routeBuilder->buildRoute($this);
		if (empty(Bag::getAccessedRoute()) && intval($this->routeBuilder->__build) !== 1) {

			if (config('router')->get('throw_404_error') == true) {
			
				$errorException = config('router')->get('404_error_exception');
			
				throw new $errorException(sprintf("Route %s not registered.", $this->getRequestUri(true)));
			
			}

			return;
		}

		// Run validation on route slugs/parameters...
		$validator = new RouteParameterValidator($this);
		$validator->dispatchValidator();

		// Get parameter validation status
		$validationStatus = $validator->getValidatorEvent();
		if ($validationStatus == 1) {
			return;
		}

		$callbackTypeValidator = new RouteCallbackTypeValidator($this);
		$callbackTypeValidator->validate();

		$callback = $callbackTypeValidator->getGeneratedCallback();

		$dispatcher = new Dispatcher($this);
		return $dispatcher->dispatch($callback);

	}

}