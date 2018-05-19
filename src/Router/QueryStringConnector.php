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

use RuntimeException;
use Kit\Http\Router\{Repository, Bag};

class QueryStringConnector
{

	/**
	* @var 		$repository
	* @access 	private
	*/
	private 	$repository;

	/**
	* @var 		$connectorList
	* @access 	private
	*/
	private static $connectorList = [];

	/**
	* Constructor method accepts $repository {Http\Router\Repository} as an argument.
	*
	* @param 	$repository Http\Router\Repository
	* @access 	public
	* @return 	void
	*/
	public function __construct(Repository $repository)
	{
		$this->Repository = $repository;
	}

	/**
	* Adds a route's query string validation rule to @param $connectorList.
	*
	* @param 	$route <String>
	* @param 	$rule <Boolean>
	* @access 	public
	* @throws 	RuntimeException
	* @return 	void
	*/
	public function setRuleFor($route, $rule=false)
	{
		if (QueryStringConnector::isQueued($route)) {
			throw new RuntimeException(app()->load('en_msg')->getMessage('query_string_rule_exists', ['route' => $route]));
		}

		QueryStringConnector::$connectorList[$route] = (Integer) $rule;
	}

	/**
	* Returns an array of all routes that has query string validation
	* set.
	*
	* @access 	public
	* @return 	Array
	*/
	public static function getList()
	{
		return QueryStringConnector::$connectorList;
	}

	/**
	* Returns a registered route's validation value.
	*
	* @param 	$route <String>
	* @access 	public
	* @return 	Integer
	*/
	public static function getValidationFor($route)
	{
		return (Integer) QueryStringConnector::$connectorList[$route];
	}

	/**
	* @param 	$route <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function isQueued($route='')
	{
		return (isset(QueryStringConnector::$connectorList[$route])) ? true : false;
	}

}