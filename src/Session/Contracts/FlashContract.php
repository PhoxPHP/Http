<?php
/**
* @author 		Peter Taiwo <peter@phoxphp.com>
* @package 		Kit\Http\Session\Contracts\FlashContract
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

namespace Kit\Http\Session\Contracts;

use Kit\Http\Session\Drivers\File\FileDriver;

interface FlashContract
{

	/**
	* Flash constructor
	*
	* @param 	$driver <Kit\Http\Session\Drivers\File\FileDriver>
	* @access 	public
	* @return 	void
	*/
	public function __construct(FileDriver $driver);

	/**
	* Set flash data.
	*
	* @param 	$label <String>
	* @param 	$message <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function set(String $label, String $message=null);

	/**
	* Check if flash exists.
	*
	* @param 	$label <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists(String $label) : Bool;

	/**
	* Returns flash message and destroys the session.
	*
	* @param 	$label <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function get(String $label);

}