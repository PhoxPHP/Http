<?php
/**
* @author 	Peter Taiwo
* @since 	1.4.5
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

class DomainBag
{

	/**
	* @var 		$domains
	* @access 	protected
	* @static
	*/
	protected static $domains = [];

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct()
	{
		//
	}

	/**
	* Add and register the route domain to bag.
	*
	* @param 	$route <String> | route name
	* @param 	$domain <String> | route domain
	* @param 	$method <String> | route request method
	* @access 	public
	* @return 	void
	* @static
	*/
	public static function registerRouteDomain(String $route, String $domain, String $method)
	{
		DomainBag::$domains[$method][$route] = [
			'domain' => $domain
		];
	}

	/**
	* Returns array of domains.
	*
	* @access 	public
	* @return 	Array
	* @static
	*/
	public static function getDomains()
	{
		return DomainBag::$domains;
	}

}