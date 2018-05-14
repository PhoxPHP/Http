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

namespace Kit\Http;

use App\AppManager;
use RuntimeException;
use Kit\View\Manager as ViewManager;

abstract class Controller
{

	/**
	* @var 		$view
	* @access 	protected
	*/
	protected 	$view;

	/**
	* @var 		$app
	* @access 	protected
	*/
	protected 	$app;

	/**
	* @var 		$request
	* @access 	private
	*/
	private 	$request;

	/**
	* @var 		$response
	* @access 	private
	*/
	private 	$response;

	/**
	* @access 	private
	* @return 	void
	*/
	public function __construct()
	{
		//
	}

	/**
	* @param 	$param
	* @access 	public
	* @return 	Object
	*/
	public function __get($param)
	{
		$response = null;

		if (property_exists($this, $param)) {
			return;
		}

		$match = preg_match('/[a-zA-Z0-9]/', $param, $result);

		if ($match) {
			// Resolve model
			if ($result[0] == 'model') {
				if (!$this->model || $this->model && gettype($this->model) !== 'object') {
					throw new RuntimeException('Cannot call model on null property.');
				}
			}
		}

		return $response;
	}

	/**
	* Sets a view variable.
	*
	* @param 	$variable <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	void
	*/
	public function setVariable(String $variable='', $value='')
	{
		$this->view()->setVariable($variable, $value);
	}

	/**
	* Returns a view variable.
	*
	* @param 	$variable <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getVariable(String $variable='')
	{
		return $this->view()->getVariable($variable);
	}

	/**
	* Renders a given view.
	*
	* @param 	$template <String>
	* @param 	$layout <String>
	* @access 	public
	* @return 	void
	*/
	public function render(String $template='', String $layout='')
	{
		return $this->view()->render($template, $layout);
	}

	/**
	* Returns instance of default view engine.
	*
	* @access 	public
	* @return 	Object
	*/
	protected function view()
	{
		$manager = new ViewManager();
		return $manager->getEngine();
	}

	/**
	* Returns an instance of Kit\Http\Request\RequestManager
	*
	* @access 	public
	* @return 	Object <Kit\Http\Request\RequestManager>
	*/
	public function request()
	{
		return app()->load('request');
	}

	/**
	* Returns an instance of Kit\Http\Request\Response
	*
	* @access 	public
	* @return 	Object <Kit\Http\Request\Response>
	*/
	public function response()
	{
		return app()->load('response');
	}

	/**
	* Registers a controller model if there is any model tied to the controller.
	* For example: PagesController and PageModel
	* The string name of the class must be returned if not null.
	*
	* @access 	public
	* @return 	Mixed
	*/
	abstract public function registerModel();

}