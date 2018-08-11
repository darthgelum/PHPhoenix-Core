<?php

namespace PHPhoenix;

/**
 * Base Controller class. Controllers contain the  logic of your website,
 * each action representing a reply to a particular request, e.g. a single page.
 * @package Core
 */
class Controller
{
	
	/**
	 * Phoenix Dependancy Container
	 * @var \PHPhoenix\Phoenix
	 */
	protected $phoenix;
	
	/**
	 * Request for this controller. Holds all input data.
	 * @var \PHPhoenix\Request
	 */
	public $request;

	/**
	 * Response for this controller. It will be updated with headers and
	 * response body during controller execution
	 * @var \PHPhoenix\Response
	 */
	public $response;

	/**
	 * If set to False stops controller execution
	 * @var boolean
	 */
	public $execute = true;

	/**
	 * This method is called before the action.
	 * You can override it if you need to,
	 * it doesn't do anything by default.
	 *
	 * @return void
	 */
	public function before()
	{

	}

	/**
	 * This method is called after the action.
	 * You can override it if you need to,
	 * it doesn't do anything by default.
	 *
	 * @return void
	 */
	public function after()
	{

	}

	/**
	 * Creates new Controller
	 *
	 */
	public function __construct($phoenix)
	{
		$this->phoenix = $phoenix;
		$this->response = $phoenix->response();
	}

	/**
	 * Shortcut for redirecting the user.
	 * Use like this:
	 * <code>
	 *     return $this->redirect($url);
	 * </code>
	 *
	 * @param string $url URL to redirect to.
	 * @return void
	 */
	public function redirect($url) {
		$this->response->redirect($url);
		$this->execute = false;
	}
	
	/**
	 * Runs the appropriate action.
	 * It will execute the before() method before the action
	 * and after() method after the action finishes.
	 *
	 * @param string    $action Name of the action to execute.
	 * @return void
	 * @throws \PHPhoenix\Exception\PageNotFound If the specified action doesn't exist
	 */
	public function run($action)
	{
		$action = 'action_'.$action;
		
		if (!method_exists($this, $action))
			throw new \PHPhoenix\Exception\PageNotFound("Method {$action} doesn't exist in ".get_class($this));
			
		$this->execute = true;
		$this->before();
		if ($this->execute)
			$this->$action();
		if ($this->execute)
			$this->after();
	}

    public function api_response($value)
    {
        $this->response->body = $value;
        $this->execute = false;
    }

    public function json_response($value)
    {
        $this->api_response(json_encode($value));
    }

}
