<?php
/**
* @author 	Peter Taiwo
* @version 	1.0.5
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

namespace Kit\Http\Session\Contracts;

interface SessionDriverContract
{

	/**
	* Sets the option whether to make the driver available or not.
	*
	* @access 	public
	* @return 	Boolean
	*/
	public function register();

	/**
	* Creates a new session.
	*
	* @param 	$key <String>
	* @param 	$value <Mixed>
	* @param 	$duration <Integer>
	* @access 	public
	* @return 	void
	*/
	public function create(String $key=null, $value=null, Int $duration=60);

	/**
	* Checks if session exists.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists(String $key=null);

	/**
	* Deletes a session from the session store.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function delete(String $key=null);

	/**
	* @access 	public
	* @return 	Mixed
	*/
	public function get();

	/**
	* Reads all session from the session store.
	*
	* @param 	$toObject <Boolean>
	* @access 	public
	* @return 	void
	*/
	public function all($toObject=false);

	/**
	* Returns the first session data from session store.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function first();

	/**
	* Returns the last session data from session store.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function last();

	/**
	* Returns the session data at offset given at @param $offset.
	*
	* @param 	$offset <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function offset(String $offset=null);

	/**
	* Deletes all session in the session store.
	*
	* @access 	public
	* @return 	void
	*/
	public function deleteAll();

	/**
	* Deletes all session in the session store with the excemption of sessions
	* provided in @param $array.
	*
	* @param 	$array <Array>
	* @access 	public
	* @return 	void
	*/
	public function deleteAllExcept(array $array=[]);

	/**
	* Returns the session configuration pulled from the factory's getConfiguration
	* method.
	*
	* @access 	public
	* @return 	Array|Object
	*/
	public function config();

	/**
	* Returns the created time of a session.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getCreatedDate(String $key=null);

	/**
	* Returns the expiration time of a session.
	*
	* @access 	public
	* @return 	Integer
	*/
	public function getTimeout(String $key=null);

	/**
	* Checks if a session has expired.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function isExpired(String $key=null);

	/**
	* Increments a session's timeout.
	*
	* @param 	$key <String>
	* @param 	$timeout <String>
	* @access 	public
	* @return 	void
	*/
	public function incrementTimeout(String $key=null, Int $timeout=60);

	/**
	* Derements a session's timeout.
	*
	* @param 	$key <String>
	* @param 	$timeout <String>
	* @access 	public
	* @return 	void
	*/
	public function decrementTimeout(String $key=null, Int $timeout=60);

	/**
	* Sets flash message.
	* 
	* @param 	$label <String>
	* @param 	$message <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function setFlash(String $label, String $message=null);

	/**
	* Checks if a flash with given label exists.
	*
	* @param 	$label <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function hasFlash(String $label) : Bool;

	/**
	* Returns a flash with given label or null if it does not exist.
	*
	* @param 	$label <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getFlash(String $label);

}