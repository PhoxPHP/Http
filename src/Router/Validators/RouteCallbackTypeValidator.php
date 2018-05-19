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

use StdClass;
use RuntimeException;
use Kit\Http\Router\Repository;

class RouteCallbackTypeValidator
{

	/**
	* @var 		$repository
	* @access 	private
	*/
	private 	$repository;

	/**
	* @var 		$typeObject
	* @access 	private
	*/
	private 	$typeObject;

	/**
	* @constant STRING_CALL_TYPE_A
	*/
	const 		STRING_CALL_TYPE_A = 'Primary String Call Type';

	/**
	* @constant STRING_CALL_TYPE_B
	*/
	const 		STRING_CALL_TYPE_B = 'Secondary String Call Type';

	public function __construct(Repository $repository)
	{
		$this->repository = $repository;
		$this->typeObject = new StdClass;
	}

	/**
	* @access 	public
	* @return 	void
	*/
	public function validate()
	{
		$route = $this->repository->getConfiguredRoute();
		$callback = $route['callback'];

		if (gettype($callback) == 'object') {
			
			$this->typeObject->type = 'object';
			$this->typeObject->callback = $callback;
			return;
		
		}

		if (gettype($callback) == 'string') {

			$typeOk = ($this->typePrimary($callback) == true || $this->typeSecondary($callback) == true);
			if (!$typeOk) {
				throw new RuntimeException(sprintf("Callback type not recognized. %s type provided.", gettype($callback)));
			}
		}
	}

	/**
	* @access 	public
	* @return 	Object
	*/
	public function getGeneratedCallback() : StdClass
	{
		return $this->typeObject;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function typePrimary($string='')
	{
		if (preg_match("/(.*[a-zA-Z0-9])\.(.*[a-zA-Z0-9])$/", $string, $match)) {
			$this->typeObject->type = 'string';
			$this->typeObject->callback = $match;
			$this->typeObject->stringType = RouteCallbackTypeValidator::STRING_CALL_TYPE_A;
			return true;
		}

		return false;
	}

	/**
	* @param 	$string <String>
	* @access 	private
	* @return 	Boolean
	*/
	private function typeSecondary($string='')
	{
		if (preg_match("/(.*[a-zA-Z0-9])@(.*[a-zA-Z0-9])$/", $string, $match)) {
			$this->typeObject->type = 'string';
			$this->typeObject->callback = $match;
			$this->typeObject->stringType = RouteCallbackTypeValidator::STRING_CALL_TYPE_B;
			return true;
		}
		
		return false;
	}

}