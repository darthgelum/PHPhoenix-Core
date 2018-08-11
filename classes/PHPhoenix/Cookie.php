<?php

namespace PHPhoenix;

/**
 * Cookie handler.
 *
 * This class only manages cookie values and parameters, but doesn't send them.
 * The Response class processes sending cookies to the client.
 *
 * @see \PHPhoenix\Response
 * @package Core
 */
class Cookie {

	/**
	 * Phoenix Dependancy Container
	 * @var \PHPhoenix\Phoenix
	 */
	protected $phoenix;
	
	/**
	 * Current cookie values
	 * @var array
	 */
	protected $cookies = array();
	
	/**
	 * Modified cookie data
	 * @var array
	 */
	protected $updates = array();
	
	/**
	 * List of available cookie parameters
	 * @var array
	 */
	protected $params = array('lifetime', 'path', 'domain', 'secure', 'http_only');
	/**
	 * Constructs the cookie handler
	 *
	 * @param \PHPhoenix\Phoenix $phoenix Phoenix dependency container
	 */
	public function __construct($phoenix) {
		$this->phoenix = $phoenix;
	}
	
	/**
	 * Sets current cookie values
	 *
	 * @param array Associative array of cookies and their values
	 */
	public function set_cookie_data($cookies) {
		$this->cookies = $cookies;
		$this->updates = array();
	}
	
	/**
	 * Gets the value of a cookie
	 *
	 * @param string $key     Cookie name
	 * @param mixed $default Default value
	 * @return mixed Cookie value
	 */
	public function get($key, $default = null)
	{
		return $this->phoenix->arr($this->cookies, $key, $default);
	}

	/**
	 * Marks a cookie for addition/replacement 
	 *
	 * If some parameters aren't supplied the defaults from /assets/cookie.php will be used.
	 *
	 * @param string $key Variable name
	 * @param mixed $val Variable value
	 * @param integer $lifetime Cookie lifetime in seconds
	 * @param string $path Cookie path
	 * @param string $domain Cookie domain
	 * @param bool $secure If cookie should be available through HTTPS only
	 * @param bool $http_only If cookie should be available inly via HTTP protocol
	 * @return void
	 */
	public function set($key, $val, $lifetime = null, $path = '/', $domain = null, $secure = null, $http_only = null) {
		$params = array();
		foreach($this->params as $param)
			if ($$param !== null)
				$params[$param] = $$param;
		
		$params['value'] = $val;
		$this->updates[$key] = $params;
		$this->cookies[$key] = $val;
	}

	/**
	 * Marks a cookie for removal
	 *
	 * @param string $key Cookie name
	 * @return void
	 */
	public function remove($key) {
		unset($this->cookies[$key]);
		$this->set($key, null, -24*3600*30);
	}
	
	/**
	 * Gets parameters for cookies that need to be updated
	 *
	 * @return array Associative array of cookies that need to be updated and their data
	 */
	public function get_updates() {
		$defaults = $this->phoenix->config->get('cookie');
		$updates = $this->updates;
		
		foreach($updates as $key => $params) {
			$params = array_merge($defaults, $params);
			
			foreach($this->params as $param)
				if (!array_key_exists($param, $params))
					$params[$param] = null;
			
			$params['expires'] = $params['lifetime'] !== null ? time() + $params['lifetime'] : null;
			unset($params['lifetime']);
			
			$updates[$key] = $params;
		}
		return $updates;
	}

}
