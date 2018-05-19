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
use App\AppManager;
use RuntimeException;
use Kit\Prop\ClassLoader;
use	Kit\Http\Router\Middleware;
use	Kit\Http\Router\Repository;
use	Kit\Http\Router\Contracts\Dispatchable;
use	Kit\Http\Router\Contracts\DispatcherContract;
use Kit\Http\Router\Validators\RouteCallbackTypeValidator;

class Dispatcher implements DispatcherContract
{

	/**
	* @var 		$controller
	* @access 	protected
	*/
	protected 	$controller;

	/**
	* @var 		$dispatchable
	* @access 	protected
	*/
	protected 	$dispatchable;

	/**
	* @var 		$appErrors
	* @access 	protected
	*/
	protected 	$appErrors = [];

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
			$this->processFilters('before');

			call_user_func_array($callback->callback, array_map(function($parameter) use ($parameters) {
					return $parameters[$parameter];
				}, $keys)
			);

			$this->processFilters('after');
		}
	}

	/**
	* @param 	$array <Array>
	* @access 	private
	* @return 	void
	*/
	private function applyStringCallback(array $array=[])
	{
		$controller = $array[1];

		if (!class_exists($controller)) {
			throw new RuntimeException(sprintf("Unable to load {%s} controller.", $controller));		
		}

		$loader = new ClassLoader();

		$this->controller = $loader->getInstanceOfClass($controller);
		$route = $this->dispatchable->getConfiguredRoute();
		$model = null;

		$action = $array[2];

		if ($action == '__construct') {
			throw new RuntimeException(
				sprintf(
					'Cannot call %s method as an action',
					'__construct'
				)
			);
		}

		if (!method_exists($this->controller, $action)) {
			throw new RuntimeException(
				sprintf(
					'Method {%s} not found in {%s} controller',
					$action,
					$controller
				)
			);
		}

		ob_start();
			$this->processFilters('before');
			$loader->callClassMethod($this->controller, $action, $route['parameters']);
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

		// Run after filters
		$this->processFilters('after');
	}

	/**
	* Invokes all filter objects.
	*
	* @param 	$type <String>
	* @access 	protected
	* @return 	void
	*/
	protected function processFilters(String $type='before')
	{
		$registeredFilters = Repository::getRegisteredFilters();
		$filters = $registeredFilters[$type] ?? null;

		if ($filters == null) {
			return;
		}

		$configFilters = config('router')->get('filters');
		foreach($filters as $handle) {
			$handle = $configFilters[$handle];
			$filter = app()->load('loader')->callClassMethod(
				new $handle(),
				'call',
				app()->load('request'),
				app()->load('response')
			);
		}
	} 

}