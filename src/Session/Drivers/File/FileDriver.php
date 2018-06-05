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

namespace Kit\Http\Session\Drivers\File;

use StdClass;
use Kit\Http\Session\Factory;
use Kit\Http\Session\Drivers\File\Flash;
use Kit\Http\Session\Drivers\File\Token;
use Kit\Http\Session\Contracts\SessionDriverContract;

trait FileDriverCommand
{

	/**
	* @param 	$command <String>
	* @access 	public
	* @return 	Mixed
	*/
	public static function runCommand($command)
	{
		$command = "?> $command <?php ";
		return eval($command);
	}

}
 
class FileDriver implements SessionDriverContract
{

	use FileDriverCommand;

	/**
	* @var 		$factory
	* @access 	protected
	*/
	protected	$factory;

	/**
	* @var 		$nullKeyTypes
	* @access 	protected
	*/
	protected 	$nullKeyTypes = ['object', 'array', 'closure', 'boolean'];

	/**
	* @var 		$defaults
	* @access 	protected
	*/
	protected 	$defaults = ['keyname' => 'app-session-store-key'];

	/**
	* @var 		$key
	* @access 	protected
	*/
	protected 	$key;

	/**
	* @var 		$offset
	* @access 	protected
	*/
	protected 	$offset;

	/**
	* @var 		$store
	* @access 	protected
	*/
	protected 	$store;

	/**
	* @var 		$shiftToTime
	* @access 	protected
	*/
	protected 	$shiftToTime = false;

	/**
	* {Constructor}
	*
	* @param 	$factory Http\Session\Factory
	* @access 	public
	* @return 	void
	*/
	public function __construct(Factory $factory) {
		$this->factory = $factory;
		$storage = $this->config()->storage;

		if (session_status() == PHP_SESSION_NONE) {
			session_save_path($storage);
			session_start();
		}
	}

	/**
	* {@inheritDoc}
	*/
	public function register()
	{
		return true;
	}

	/**
	* {@inheritDoc}
	*/
	public function create(String $key=null, $value=null, Int $duration=60)
	{
		if ($this->exists($key)) {
			return;
		}

		$timeout = (is_integer($duration)) ? $duration : $this->getTimestamp($duration);

		if (in_array(gettype($key), $this->nullKeyTypes)) {
			$key = 'app-session-store-key';
		}

		$_SESSION[$key] = [
			$this->encrypt($key) => $value,
			't' => time() . '|' . $timeout
		];
	}

	/**
	* {@inheritDoc}
	*/
	public function exists(String $key=null)
	{
		if ($key !== null) {
			$keys = explode('|', $key);
			$reader = $this->read($keys[0] . '|' . $this->encrypt($keys[0]));
			array_shift($keys);

			if (!empty($keys)) {
				$reader .= $this->read(
					implode('|', $keys)
				);
			}

			if ($this->shiftToTime == true) {
				$reader = $this->read($key);
			}

			$cmd = '<?php return isset($_SESSION'. $reader .'); ?>';
			$cmd = FileDriverCommand::runCommand($cmd);
		
			if ($cmd) {
				return true;
			}

			return false;
		}

		return false;
	}

	/**
	* {@inheritDoc}
	*/
	public function delete(String $key=null)
	{
		if ($this->exists($key)) {
			$cmd = '<?php unset($_SESSION'. $this->read($key) .'); ?>';
			return FileDriverCommand::runCommand($cmd);
		}
	}

	/**
	* {@inheritDoc}
	*/
	public function get() : SessionDriverContract
	{
		$this->store = $this->store();
		return $this;
	}

	/**
	* {@inheritDoc}
	*/
	public function all($toObject=false)
	{
		$response = $_SESSION;

		if (boolval($toObject) == true) {
			$response = (Object) $response;
		}

		return $response;
	}

	/**
	* {@inheritDoc}
	*/
	public function first()
	{
		if (sizeof($_SESSION) < 1) {
			return null;
		}

		foreach(array_keys($_SESSION) as $i => $key) {
			if ($i == 0) {
				return [
					$key => $_SESSION[$key][$this->encrypt($key)]
				];
			}
		}
	}

	/**
	* {@inheritDoc}
	*/
	public function last()
	{
		if (sizeof($_SESSION) < 1) {
			return;
		}
	
		$lastOffset = sizeof($_SESSION) - 1;
	
		foreach(array_keys($_SESSION) as $i => $key) {
			if ($i == $lastOffset) {
				return [
					$key => $_SESSION[$key][$this->encrypt($key)]
				];
			}	
		}
	}

	/**
	* {@inheritDoc}
	*/
	public function offset(String $key=null)
	{
		if ($this->exists($key)) {

			// If session has expired, a null is returned.
			if ($this->isExpired($key)) {
				return null;
			}

			$keys = explode('|', $key);
			$reader = $this->read($keys[0] . '|' . $this->encrypt($keys[0]));
			array_shift($keys);

			if (!empty($keys)) {
				$reader .= $this->read(
					implode('|', $keys)
				);
			}

			if ($this->shiftToTime == true) {
				$reader = $this->read($key);
			}

			$cmd = '<?php return $_SESSION'. $reader .'; ?>';
			$cmd = FileDriverCommand::runCommand($cmd);
			return $cmd;
		}

		return false;
	}

	/**
	* {@inheritDoc}
	*/
	public function deleteAll()
	{
		if (sizeof($_SESSION) > 0) {
			session_destroy();
			session_unset();
		}

		return true;
	}

	/**
	* {@inheritDoc}
	*/
	public function deleteAllExcept(array $whiteList=[])
	{
		if (sizeof($_SESSION) > 0) {

			$sessionKeys = array_keys($_SESSION);
		
			array_map(function($key) use ($whiteList) {

				$encryptedKey = $this->encrypt($key);
				
				if (!in_array($key, $whiteList) && isset($_SESSION[$key])) {

					unset($_SESSION[$key]);

				}

			}, array_keys($_SESSION));

		}

		return true;
	}

	/**
	* {@inheritDoc}
	*/
	public function config()
	{
		return $this->factory->getConfiguration();
	}

	/**
	* {@inheritDoc}
	*/
	public function getCreatedDate(String $key=null)
	{
		$this->shiftToTime = true;
		if (sizeof(explode('|', $key)) > 1 || !$this->exists($key) || !$this->exists($key . '|t')) {
			return false;
		}

		$key .= '|t';
		$time = explode('|', $this->offset($key));
		return $time[0];
	}

	/**
	* {@inheritDoc}
	*/
	public function getTimeout(String $key=null)
	{
		$this->shiftToTime = true;
		if (sizeof(explode('|', $key)) > 1 || !$this->exists($key) || !$this->exists($key. '|t')) {
			return false;
		}

		$key .= '|t';
		$time = explode('|', $this->offset($key));
		return $time[1];
	}

	/**
	* {@inheritDoc}
	*/
	public function isExpired(String $key=null)
	{
		$response = false;
		if (!$this->exists($key)) {
			return true;
		}

		$this->shiftToTime = true;
		if (!$this->exists($key) || !$this->exists($key. '|t')) {
			return false;
		}

		$time = explode('|', $_SESSION[$key]['t']);

		if (time() > bcadd($time[0], $time[1])) {
			$response = true;
		}
		
		$this->shiftToTime = false;
		return $response;
	}

	/**
	* {@inheritDoc}
	*/
	public function incrementTimeout(String $key=null, Int $timeout=60)
	{
		if (!$this->exists($key)) {
			return;
		}

		$time = $this->getSessionTime($key);
		$duration = (is_int($timeout)) ? $timeout : $this->getTimestamp($timeout);
		$duration = bcadd($time[1], $duration);

		$_SESSION[$key]['t'] = $time[0] . '|' . $duration;
	}

	/**
	* {@inheritDoc}
	*/
	public function decrementTimeout(String $key=null, Int $timeout=60)
	{
		if (!$this->exists($key)) {
			return;
		}

		$time = $this->getSessionTime($key);
		$duration = (is_int($timeout)) ? $timeout : $this->getTimestamp($timeout);
		
		if ($duration > $time[1]) {
			return;
		}

		$duration = bcsub($time[1], $duration);
		$_SESSION[$key]['t'] = $time[0] . '|' . $duration;
	}

	/**
	* {@inheritDoc}
	*/
	public function setFlash(String $label, String $message=null)
	{
		$flash = new Flash($this);
		return $flash->set($label, $message);
	}

	/**
	* {@inheritDoc}
	*/
	public function hasFlash(String $label) : Bool
	{
		$flash = new Flash($this);
		return $flash->exists($label);
	}

	/**
	* {@inheritDoc}
	*/
	public function getFlash(String $label)
	{
		$flash = new Flash($this);
		return $flash->get($label);
	}

	/**
	* {@inheritDoc}
	*/
	public function getToken() : String
	{
		return (new Token($this))->generateAndReturnToken();
	}

	/**
	* {@inheritDoc}
	*/	
	public function verifyToken() : Bool
	{
		if ((new Token($this))->verifyToken()) {
			return true;
		}

		return false;
	}

	/**
	* Returns a session's time value.
	*
	* @param 	$key <String>
	* @access 	protected
	* @return 	Array
	*/
	protected function getSessionTime(String $key='')
	{
		$this->shiftToTime = true;
		if (sizeof(explode('|', $key)) > 1 || !$this->exists($key) || !$this->exists($key. '|t')) {
			return false;
		}

		$time = $key . '|t';
		return explode('|', $this->offset($time));
	}

	/**
	* Returns object of php's global session variable.
	*
	* @access 	protected
	* @return 	Object
	*/
	protected function store()
	{
		return (Object) $_SESSION;
	}

	/**
	* Formats and returns a session string.
	*
	* @param 	$string <String>
	* @access 	protected
	* @return 	String
	*/
	protected function read($string='')
	{
		$string = explode('|', $string);
		$queue = [];

		foreach($string as $str) {
			$queue[] = '["'.$str.'"]';
		}

		return implode('', $queue);
	}

	/**
	* @param 	$timeout <Integer>
	* @access 	protected
	* @return 	Integer
	*/
	protected function getTimestamp($timeout)
	{
		$factoryTimeout = $this->config()->timeout;
		
		if (!is_int($factoryTimeout) || intval($factoryTimeout) < 1) {
			$factoryTimeout = $timeout;
		}

		return $factoryTimeout;
	}

	/**
	* Encrypts a session key using md5 and sha1 functions.
	*
	* @param 	$key <String>
	* @access 	protected
	* @return 	String
	*/
	protected function encrypt(String $key='')
	{
		return md5(sha1($key));
	}

}