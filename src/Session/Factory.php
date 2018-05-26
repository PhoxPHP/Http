<?php
/**
* @author 	Peter Taiwo
* @version 	1.1.0
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

namespace Kit\Http\Session;

use App\AppManager;
use ReflectionClass;
use RuntimeException;
use \Kit\Http\Session\Contracts\SessionDriverContract;

class Factory
{

	/**
	* @var 		$driver
	* @access 	private
	*/
	private 	$driver;

	/**
	* @access 	public
	* @return 	void
	*/
	public function __construct()
	{
		$this->resolveDriver();
	}

	/**
	* Resolves driver by checking if it is allowed.
	*
	* @access 	protected
	* @return 	void
	*/
	protected function resolveDriver()
	{
		$driver = $this->getDriver();

		if (gettype($driver->register()) !== 'boolean' || boolval($this->getDriver()) !== true) {
		
			return;
		
		}
	}

	/**
	* Adds a new session to the session store.
	*
	* @param 	$key <String> Session key to be added.
	* @param 	$value <String> Session value of key to be added.
	* @param 	$timeout <Integer> Session alive time in seconds.
	* @access 	private
	* @return 	void
	*/
	public function create($key='', $value='', Int $timeout=60)
	{
		return $this->getDriver()->create($key, $value, $timeout);
	}

	/**
	* Checks if a session exists in the session store.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function exists($key='')
	{
		return $this->getDriver()->exists($key);
	}

	/**
	* Removes a session from the session store.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	void
	*/
	public function delete($key='')
	{
		return $this->getDriver()->delete($key);
	}

	/**
	* Reads a session from the session store using the session key @param $key.
	*
	* @param 	$Key <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function get($key='')
	{
		return $this->getDriver()->get()->offset($key);
	}

	/**
	* Reads all session data from the session store.
	*
	* @param 	$toObject <Boolean> The sessions will be read as an array but if this parameter is set
	* to true, the data will return an object.
	* @access 	public
	* @return 	Array|Object
	*/
	public function getAll($toObject=false)
	{
		return $this->getDriver()->get()->all($toObject);
	}

	/**
	* Returns the first session data in the session store.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getFirst()
	{
		return $this->getDriver()->get()->first();
	}

	/**
	* Returns the last session data in the session store.
	*
	* @access 	public
	* @return 	Mixed
	*/
	public function getLast()
	{
		return $this->getDriver()->get()->last();
	}

	/**
	* Deletes all session from the session store.
	*
	* @access 	public
	* @return 	void
	*/
	public function deleteAll()
	{
		return $this->getDriver()->deleteAll();
	}

	/**
	* Deletes all sessions in the session store except for the sessions associated
	* with the keys provided @param $array.
	*
	* @param 	$array <Array>
	* @access 	public
	* @return 	void
	*/
	public function deleteAllExcept(array $array=[])
	{
		return $this->getDriver()->deleteAllExcept($array);
	}

	/**
	* Returns created date of a session.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getCreatedDate(String $key='')
	{
		return $this->getDriver()->getCreatedDate($key);
	}

	/**
	* Returns timeout of a session.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Integer
	*/
	public function getTimeout(String $key='')
	{
		return $this->getDriver()->getTimeout($key);
	}

	/**
	* Checks if a session has expired.
	*
	* @param 	$key <String>
	* @access 	public
	* @return 	Boolean
	*/
	public function isExpired(String $key='')
	{
		return $this->getDriver()->isExpired($key);
	}

	/**
	* @param 	$key <String>
	* @param 	$timeout <Integer>
	* @access 	public
	* @return  	void
	*/
	public function incrementTimeout(String $key='', $timeout=60)
	{
		return $this->getDriver()->incrementTimeout($key, $timeout);
	}

	/**
	* @param 	$key <String>
	* @param 	$timeout <Integer>
	* @access 	public
	* @return 	void
	*/
	public function decrementTimeout(String $key='', $timeout=60)
	{
		return $this->getDriver()->decrementTimeout($key, $timeout);
	}

	/**
	* Returns the session driver in use.
	*
	* @access 	public
	* @return 	String
	*/
	public function getDriverName()
	{
		return $this->getConfiguration()->driver;
	}

	/**
	* Returns the class name of the driver that is being used.
	*
	* @access 	public
	* @return 	String
	*/
	public function getClass()
	{
		$driver = $this->config()->driver;
		return "Http\\Session\\Driver\\$driver"."Driver";
	}

	/**
	* Sets a flash message.
	*
	* @param 	$label <String>
	* @param 	$message <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function setFlash(String $label, String $message=null)
	{
		return $this->getDriver()->setFlash($label, $message);
	}

	/**
	* Checks if flash exists with given label.
	*
	* @param 	$label <String> 	
	* @access 	public
	* @return 	Boolean
	*/
	public function hasFlash(String $label) : Bool
	{
		return $this->getDriver()->hasFlash($label);
	}

	/**
	* Returns flash with given label.
	*
	* @param 	$label <String>
	* @access 	public
	* @return 	Mixed
	*/
	public function getFlash(String $label)
	{
		return $this->getDriver()->getFlash($label);
	}

	/**
	* Generates and returns a csrf token.
	*
	* @access 	public
	* @return 	String
	*/
	public function token(String $formOrRequestName=null)
	{
		return $this->getDriver()->getToken($formOrRequestName);
	}

	/**
	* Verifies a csrf token.
	*
	* @access 	public
	* @return 	String
	*/
	public function verifyToken()
	{
		return $this->getDriver()->verifyToken();
	}

	/**
	* Returns the object of the session driver in use.
	*
	* @access 	protected
	* @return 	Object
	*/
	protected function getDriver() : SessionDriverContract
	{
		$driver = $this->getConfiguration()->driver;
		
		if (class_exists($driver)) {

			$driverObject = new $driver($this);
			$driverContract = SessionDriverContract::class;

			if (!$driverObject instanceof $driverContract) {
				throw new RuntimeException(sprintf("Invalid session driver object. Driver must implement %s.", $driverContract));
			}

			return new $driver($this);
		}
	}

	/**
	* Returns an array of the session configuration.
	*
	* @access 	public
	* @return 	Object
	*/
	public function getConfiguration()
	{
		$config = app()->load('config')->get('session');
		
		if (gettype($config) !== 'array') {

			throw new RuntimeException(sprintf("Invalid session configuration provided. Array expected, %s given.", gettype($config)));
		
		}

		return (Object) $config;
	}

}