<?php

namespace PHPhoenix\View;

/**
 * View helper class.
 * An instance of this class is passed automatically
 * to every View.
 *
 * You can extend it to make your own methods available in view templates.
 *
 * @package Core
 */
class Helper {

	/**
	 * Phoenix Dependancy Container
	 * @var \PHPhoenix\Phoenix
	*/
	protected $phoenix;
	
	/**
	 * Constructs the view helper
	 * @param \PHPhoenix\Phoenix $phoenix Phoenix dependency container
	 */
	public function __construct($phoenix) {
		$this->phoenix = $phoenix;
		
	}
	
	/**
	 * List of aliases to create for methods
	 * @var array
	 */
	protected $aliases = array(
		'_' => 'output'
	);
	
	/**
	 * Gets the array of aliases to helper methods
	 * 
	 * @return array Associative array of aliases mapped to their methods
	 */
	public function get_aliases() {
		$aliases = array();
		foreach($this->aliases as $alias => $method)
			$aliases[$alias] = array($this, $method);
		return $aliases;
	}
	
	/**
	 * Escapes string to safely display HTML entities
	 * like < > & without breaking layout and prevent XSS attacks.
	 *
	 * @param string $str String to escape
	 * @return string Escaped string.
	 */
	public function escape($str) {
		return htmlentities($str);
	}
	
	/**
	 * Escapes and prints a string.
	 *
	 * @param string $str String to escape
	 * @see \PHPhoenix\View\Helper::escape
	 */
	public function output($str) {
		echo $this->escape($str);
	}
}
