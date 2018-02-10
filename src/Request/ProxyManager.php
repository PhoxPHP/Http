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

namespace Kit\Http\Request;

class ProxyManager
{

	/**
	* @var 		$proxies
	* @access 	private
	*/
	private static $proxies = [];

	/**
	* @var 		$status
	* @access 	private
	*/
	private static $status = false;

	/**
	* @var 		$defaultProxyIp
	* @access 	public
	*/
	public static $defaultProxyIp = '127.0.0.1';

	/**
	* @var 		$defaultProxyPort
	* @access 	public
	*/
	public static $defaultProxyPort = 80;

	/**
	* Constructor
	*
	* @param 	$name <String>
	* @param 	$address <String>
	* @param 	$port <String>
	* @access 	public
	* @return 	void
	*/
	public static function createProxy($name='', $address='', $port='')
	{
		(Array) ProxyManager::$proxies[$name] = ['address' => $address, 'port' => $port];
		(Boolean) ProxyManager::$status = true;
	}

	/**
	* @access 	public
	* @return 	Boolean
	*/
	public static function getStatus()
	{
		return (Boolean) ProxyManager::$status;
	}

	/**
	* @param 	$name <String>
	* @access 	public
	* @return 	Mixed
	*/
	public static function getProxy($name='')
	{
		$response = null;

		if (ProxyManager::exists($name)) {
		
			$response = ProxyManager::$proxies[$name];
		
		}

		return $response;
	}

	/**
	* @param 	$name <String>
	* @access 	public
	* @return 	Boolean
	*/
	public static function exists($name='')
	{
		$response = false;
		
		if (isset(ProxyManager::$proxies[$name])) {

			$response = true;
		
		}

		return $response;
	}

}