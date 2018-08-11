<?php

namespace PHPhoenix;

/**
 * Session handler
 * @package Core
 */
class Session
{

	/**
	 * Phoenix Dependancy Container
	 * @var \PHPhoenix\Phoenix
	 */
	protected $phoenix;
	
	/**
	 * Constructs session handler
	 *
	 * @param \PHPhoenix\Phoenix $phoenix Phoenix dependency container
	 */
	public function __construct($phoenix) {
		$this->phoenix=$phoenix;
	}
	/**
	 * Makes sure the session is initialized
	 *
	 * @return void
	 */
	private function check()
	{
		if (!session_id())
			session_start();
	}

	/**
	 * Gets a session variable
	 *
	 * @param string $key Variable name, 
	 *                    If null all variables will be returned.
	 * @param mixed $default Default value
	 * @return mixed Session value
	 */
	public function get($key = null, $default = null)
	{
		$this->check();
		if($key === null)
			return $_SESSION;
		return $this->phoenix->arr($_SESSION, $key, $default);
	}

	/**
	 * Sets a session variable
	 *
	 * @param string $key Variable name
	 * @param mixed $val Variable value
	 * @return void
	 */
	public function set($key, $val)
	{
		$this->check();
		$_SESSION[$key] = $val;
	}

	/**
	 * Removes a session variable
	 *
	 * @param string $key Variable name
	 * @return void
	 */
	public function remove($key)
	{
		$this->check();

		if (!isset($_SESSION[$key]))
			return;

		$var = $_SESSION[$key];
		unset($_SESSION[$key], $var);
	}

	/**
	 * Resets the session
	 *
	 * @return void
	 */
	public function reset()
	{
		$this->check();
		$_SESSION = array();
	}

	/**
	 * Gets ot sets flash messages.
	 * If the value parameter is passed the message is set, otherwise it is retrieved.
	 * After the message is retrieved for the first time it is removed.
	 *
	 * @param $key  The name of the flash message
	 * @param $val  Flash message content
	 * @return mixed
	 */
	public function flash($key, $val = null)
	{
		$this->check();
		$key = "flash_{$key}";
		if ($val != null)
		{
			$this->set($key, $val);
		}
		else
		{
			$val = $this->get($key);
			$this->remove($key);
		}

		return $val;
	}

}
