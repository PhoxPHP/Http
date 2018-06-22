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

namespace Kit\Http\Router\Exceptions;

use App\BaseException;
use App\BaseException;
use App\Exceptions\Contract\ExceptionContract;

class InvalidValidatorSizeException extends BaseException implements ExceptionContract
{

	/**
	* @var 		$code
	* @access 	public
	*/
	public 		$code = 500;

	/**
	* @var 		$message
	* @access 	public
	*/
	public 		$message;

	/**
	* @var 		$view
	* @access 	public
	*/
	public 		$view;

	/**
	* @param 	$options <Array>
	* @access 	public
	* @return 	void
	*/
	public function __construct(...$options)
	{
		$this->setView('exception');
		$this->setMessage($options[0]);
	}

	/**
	* {@inheritDoc}
	*/
	public function setCode(int $code)
	{
		$this->code = $code;
	}

	/**
	* {@inheritDoc}
	*/
	public function setMessage(String $message)
	{
		$this->message = $message;
	}

	/**
	* {@inheritDoc}
	*/
	public function setView(String $view)
	{
		$this->view = $view;
	}

}