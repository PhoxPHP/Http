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

namespace Kit\Http\Router\Validators;

use Kit\Http\Router\Repository;
use Kit\Http\Router\Validators\Bag as ValidatorsRepo;
use Kit\Http\Router\Exceptions\InvalidValidatorSizeException;
use Kit\Http\Router\Validators\Contracts\ValidatorContract;

class RouteParameterValidator implements ValidatorContract
{

	/**
	* @var 		$validatorEventTrigger
	* @access 	private
	*/
	private 	$validatorEventTrigger=false;

	/**
	* @var 		$repository
	* @access 	private
	*/
	private 	$repository;

	/**
	* @param 	$repository Http\Router\Repository
	* @access 	public
	* @return 	void
	*/
	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
	}

	/**
	* @access 	public
	* @return 	String
	*/
	public function getValidatorEvent()
	{
		return intval($this->validatorEventTrigger);
	}

	/**
	* This method is used to validate url slugs/parameters based on the configuration
	* set in regsitered routes.
	*
	* @todo 	Return fallback function if route slug/parameter does not match.
	* @access 	public
	* @return 	void
	*/
	public function dispatchValidator()
	{
		$validatorsRepo = new ValidatorsRepo();
		$canValidate = $this->repository->config('Router', 'allow_slug_validation');

		if (!$canValidate) {
		
			return;
		
		}

		$configuredRoute = (Object) $this->repository->getConfiguredRoute();
		$route = $configuredRoute->route;

		$callback = $configuredRoute->callback;
		$slugs = $configuredRoute->parameters;
		
		$validators = $configuredRoute->validator;
		$sharedMethod = $configuredRoute->shared_method;


		array_map(function($key) use ($validators, $slugs, $validatorsRepo, $sharedMethod, $route) {

			if (array_key_exists($key, $validators)) {
				$validator = $validators[$key];
				$validatorLength = strlen($validator);

				if ($validator[0] !== "/") {
				
					$validator = "/$validator";
				
				}

				if ($validator[$validatorLength - 1] !== "/") {
					$validator = "$validator/";
				}

				$validatorFallbackObjectArguments = $this->repository->config('Router', 'slug_validation_options');

				if (!preg_match($validator, $slugs[$key])) {
					
					$route = "/$route";
					$closures = $validatorsRepo->from($sharedMethod)->getClosure($route);
					$this->validatorEventTrigger = true;

					if ($closures) {
					
						array_map(function($closure) use ($validatorFallbackObjectArguments, $slugs) {
					
							$arguments = $validatorFallbackObjectArguments['fallback_method_default_arguments'];
							return call_user_func_array($closure, array_map(function($slug) { return $slug; }, array_values($slugs)));
					
						}, $closures);
					
						return true;
					}

					trigger_error(app()->load('en_msg')->getMessage('route_param_failed', ['param' => $slugs[$key], 'param_value' => $validator]));
					return false;
				}
			}

		}, array_keys($slugs));
	}

}