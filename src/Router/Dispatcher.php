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

use	StdClass;
use App\Finder;
use App\AppManager;
use RuntimeException;
use Kit\Prop\ClassLoader;
use	Kit\Http\Router\Repository;
use	Kit\Http\Router\Interfaces\Dispatchable;
use	Kit\Http\Router\Interfaces\DispatcherInterface;
use Kit\Http\Router\Validators\RouteCallbackTypeValidator;

class Dispatcher implements DispatcherInterface
{

	/**
	* @var 		$controller
	* @access 	protected
	*/
	protected 	$controller;

	/**
	* @var 		$model
	* @access 	protected
	*/
	protected 	$model;

	/**
	* @var 		$dispatchable
	* @access 	private
	*/
	private 	$dispatchable;

	/**
	* @var 		$appErrors
	* @access 	private
	*/
	private 	$appErrors = [];

	/**
	* Construct method accepts the requires the dispatchable interface where
	* it generates the route callbacks from.  
	*
	* @param 	$dispatchable Kit\Http\Router\Repository
	* @access 	public
	* @return 	void
	*/
	public function __construct(Dispatchable $dispatchable)
	{
		$this->dispatchable = $dispatchable;
		$this->appErrors = AppManager::getErrors();
	}

	/**
	* @param 	$callback <Object>
	* @access 	public
	* @return 	void
	*/
	public function dispatch(StdClass $callback)
	{
		$configuredRoute = $this->dispatchable->getConfiguredRoute();
		$parameters = $configuredRoute['parameters'];
		$keys = array_keys($configuredRoute['parameters']);

		if ($callback->type == 'string') {

			return $this->applyStringCallback($callback->callback);
		
		}else{
		
			return call_user_func_array($callback->callback,  array_map(function($parameter) use ($parameters) {
				return $parameters[$parameter];
			}, $keys));
		
		}

	}

	/**
	* @param 	$array <Array>
	* @access 	private
	* @return 	void
	*/
	private function applyStringCallback(array $array=[])
	{
		// Dispatch......
		$controller = $array[1];

		if (!class_exists($controller)) {

			throw new RuntimeException(sprintf("Unable to load {%s} controller.", $controller));
		
		}

		$this->controller = new $controller();

		/**
		* After creating an instance of the controller called, we'll check to see if the
		* controller has the required "routeParams" property. This property will be used to access
		* the parameters returned from the route.
		*/
		if (!property_exists($this->controller, 'routeParams')) {

			throw new RuntimeException(app()->load('en_msg')->getMessage('no_default_route_param', ['controller' => $controller]));
		
		}

		$route = $this->dispatchable->getConfiguredRoute();
		$this->controller->routeParams = $route['parameters'];
		$model = null;

		if (gettype($this->controller->registerModel()) == 'string') {
			$model = $this->controller->registerModel();
			if (!class_exists($model)) {
				throw new RuntimeException(app()->load('en_msg')->getMessage('no_model_found', ['model' => $model]));				
			}

			$this->controller->model = new $model();
		}

		$finder = new Finder;
		$action = $array[2];		

		if (!method_exists($this->controller, $action)) {

			throw new RuntimeException(sprintf("Method {%s} not found in {%s} controller", $action, $controller));
		
		}

		ob_start();
		call_user_func_array([
			$this->controller,
			$action
		], $route['parameters']);
		$data = ob_get_contents();
		ob_end_clean();

		$this->appErrors = AppManager::getErrors();

		if (sizeof($this->appErrors) > 0) {

			foreach($this->appErrors as $error) {
				AppManager::getInstance()->shutdown($error['number'], $error['message'], $error['file'], $error['line']);
			}

			exit;
		}

		eval("?> $data <?php ");
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function getControllerName($string='')
	{
		return (String) $string.'Controller';
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	String
	*/
	private function getViewName($string='')
	{
		return (String) $string.'View';
	}

}