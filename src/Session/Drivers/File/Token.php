<?php
/**
* @author 	Peter Taiwo
* @since 	v1.5.0
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

namespace Kit\Http\Session\Drivers\File;

use Kit\Http\Session\Drivers\File\FileDriver;
use Kit\Http\Session\Contracts\FlashContract;

class Token
{

	/**
	* @var 		$driver
	* @access 	protected
	*/
	protected	$driver;

	/**
	* Token constructor.
	*

	* @access 	public
	* @return 	void
	*/
	public function __construct(FileDriver $driver)
	{
		$this->driver = $driver;
	}

	/**
	* Generate and return token.
	*
	* @param 	$driver <Kit\Http\Session\Drivers\File\FileDriver>
	* @access 	public
	* @return 	String
	*/
	public function generateAndReturnToken() : String
	{
		$token = base64_encode(openssl_random_pseudo_bytes(32));
		$this->driver->delete(config('session')->get('csrf_token_input_name'));
		$this->driver->create(
			config('session')->get('csrf_token_input_name'),
			$token,
			86400
		);

		return $token;
	}

}