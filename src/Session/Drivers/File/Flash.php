<?php
/**
* @author 		Peter Taiwo <peter@phoxphp.com>
* @package 		Kit\Http\Session\Drivers\File\Flash
* @license 		MIT License
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE.
*/

namespace Kit\Http\Session\Drivers\File;

use Kit\Http\Session\Drivers\File\FileDriver;
use Kit\Http\Session\Contracts\FlashContract;

class Flash implements FlashContract
{
	
	/**
	* @var 		$driver
	* @access 	protected
	*/
	protected 	$driver;

	/**
	* {@inheritDoc}
	*/
	public function __construct(FileDriver $driver)
	{
		$this->driver = $driver;
	}

	/**
	* {@inheritDoc}
	*/
	public function set(String $label, String $message=null)
	{
		$flashQueue = [];

		// Only set flash data if it does not exist. No checking needs to be made.
		if (!$this->exists($label)) {
			$_SESSION['flash'][$label] = $message;
		}
	}

	/**
	* {@inheritDoc}
	*/
	public function exists(String $label) : Bool
	{
		if (isset($_SESSION['flash'][$label])) {
			return true;
		}

		return false;
	}

	/**
	* {@inheritDoc}
	*/
	public function get(String $label)
	{
		if (isset($_SESSION['flash'][$label])) {
			$data = $_SESSION['flash'][$label];

			unset($_SESSION['flash'][$label]);
			return $data;
		}
	}

}