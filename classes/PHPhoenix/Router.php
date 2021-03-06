<?php
namespace PHPhoenix;

/**
 * Router for matching URLs to corresponding Routes
 * @package Core
 */
class Router {

	/**
	 * Phoenix Dependancy Container
	 * @var \PHPhoenix\Phoenix
	 */
	protected $phoenix;
	
	/**
	 * Associative array of route instances.
	 * @var array
	 */
	public $routes = array();

	/**
	 * Container for route's rule to process in callback function
	 */
	protected $temp_rule;

	/**
	 * Constructs a router
	 *
	 * @param \PHPhoenix\Phoenix $phoenix Phoenix dependency container
	 */
	public function __construct($phoenix) {
		$this->phoenix = $phoenix;
	}

	
	/**
	 * Ads a route
	 *
	 * @param string $name     Name of the route. Routes with the same name will override one another.
	 * @param mixed $rule     Either an expression to match URI against or a function that will
	 *                        be passed the URI and must return either an associative array of
	 *                        extracted parameters (if it matches) or False.
	 * @param array   $defaults An associated array of default values.
	 * @return void
	 */
	public function add($route)
	{
		$this->routes[$route->name] = $route;
	}

	/**
	 * Gets route by name
	 *
	 * @param string $name Route name
	 * @return \PHPhoenix\Route
	 * @throws \Exception If specified route doesn't exist
	 */
	public function get($name)
	{
		if (!isset($this->routes[$name]))
			throw new \Exception("Route {$name} not found.");

		return $this->routes[$name];
	}

	/**
	 * Matches the URI against available routes to find the correct one.
	 *
	 * @param string   $uri Request URI
	 * @param string   $method Request method
	 * @return array Array containing route and matched parameters
	 * @throws \PHPhoenix\Exception\PageNotFound If no route matches the URI
	 * @throws \PHPhoenix\Exception\PageNotFound If route matched but no Controller was defined for it
	 * @throws \PHPhoenix\Exception\PageNotFound If route matched but no action was defined for it
	 */
	public function match($uri, $method = 'GET')
	{
		$matched = false;
		$method = strtoupper($method);
		foreach ($this->routes as $name => $route) {
			if ($route-> methods != null && !in_array($method, $route->methods))
				continue;
			
			$rule = $route->rule;
			if (is_callable($rule))
			{
				if (($data = $rule($uri)) !== FALSE)
				{
					$matched = $name;
					break;
				}
			}
			else
			{
				$pattern = is_array($rule) ? $rule[0] : $rule;
				$pattern = str_replace(')', ')?', $pattern);
				$this->temp_rule = $rule;
				$pattern = preg_replace_callback('/<.*?>/', array($this, 'rule'), $pattern);

				preg_match('#^'.$pattern.'/?$#', $uri, $match);
				if (!empty($match[0]))
				{
					$matched = $name;
					$data = array();
					foreach ($match as $k => $v)
						if (!is_numeric($k))
							$data[$k] = $v;
					break;
				}
			}
		}
		if ($matched == false)
			throw new \PHPhoenix\Exception\PageNotFound('No route matched your request');
			
		$route = $this->routes[$matched];
		$params = array_merge($route->defaults, $data);
		
		if (!isset($params['controller']))
			throw new \PHPhoenix\Exception\PageNotFound("Route {$matched} matched, but no controller was defined for this route");
			
		if (!isset($params['action']))
			throw new \PHPhoenix\Exception\PageNotFound("Route {$matched} matched with controller {$params['controller']}, but no action was defined for this route");

		return array(
					'route'=>$route, 
					'params'=>$params
					);
	}

	/**
	 * This method is used only by preg_replace_callback() in method match() instead of making anonymous function
	 * to avoid fatal memory leak on some server configurations
	 */
	protected function rule($str) {
		$str = $str[0];
		$regexp = '[a-zA-Z0-9\-\._]+';
		if(is_array($this->temp_rule))
			$regexp = $this->phoenix->arr($this->temp_rule[1], str_replace(array('<', '>'), '', $str), $regexp);
		return '(?P'.$str.$regexp.')';
	}

}
