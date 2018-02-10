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
use Kit\Http\Request;
use Kit\View\Manager as ViewManager;
use Kit\DependencyInjection\Injector\InjectorBridge;

abstract class Controller extends InjectorBridge
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
	* @access 	protected
	*/
	protected 	$request;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct()
	{
		$this->app = AppManager::getInstance();
		$this->request = $this->app->load('request');
		$manager = new ViewManager();
		$this->view = $manager->getEngine();
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

		$match = preg_match('/get.*[a-zA-Z0-9]/', $param, $result);

		if ($match) {

			$res = str_replace('get', '', $result[0]);
			
			if (class_exists($res.'Controller')) {
			
				$response = new $res.'Controller';
			
			}
		}

		return $response;
	}

	/**
	* @param 	$variable <String>
	* @param 	$value <Mixed>
	* @access 	public
	* @return 	void
	*/
	public function setVariable($variable='', $value='')
	{
		$this->view->setVariable($variable, $value);
	}

	/**
	* @param 	$variable <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getVariable($variable='')
	{
		return $this->view->getVariable($variable);
	}

	/**
	* @param 	$template <String>
	* @param 	$layout <String>
	* @access 	public
	* @return 	void
	*/
	public function render($template='', $layout='')
	{
		return $this->view->render($template, $layout);
	}

}