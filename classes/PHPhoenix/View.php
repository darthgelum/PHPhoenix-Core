<?php

namespace PHPhoenix;

/**
 * Manages passing variables to templates and rendering them
 * @package Core
 */
class View
{
	/**
	 * Phoenix Dependancy Container
	 * @var \PHPhoenix\Phoenix
	 */
	protected $phoenix;
	
	/**
	 * View helper
	 * @var \PHPhoenix\View\Helper
	 */
	protected $helper;
	
	/**
	 * Full path to template file
	 * @var string
	 */
	protected $path;

	/**
	 * The name of the view.
	 * @var string
	 */
	public $name;

	/**
	 * Stores all the variables passed to the view
	 * @var array
	 */
	public $_data = array();

	/**
	 * File extension of the templates
	 * @var string
	 */
	protected $_extension = 'php';

	/**
	 * Constructs the view
	 *
	 * @param \PHPhoenix\Phoenix $phoenix Phoenix dependency container
	 * @param \PHPhoenix\View\Helper View Helper
	 * @param string   $name The name of the template to use
	 */
	public function __construct($phoenix, $helper, $name)
	{
		$this->phoenix = $phoenix;
		$this->helper = $helper;
		$this->set_template($name);
	}
	
	/**
	 * Sets the template to use for rendering
	 *
	 * @param string   $name The name of the template to use
	 * @throws \Exception If specified template is not found
	 */
	public function set_template($name) {
		$this->name = $name;
		
		$file = $this->phoenix->find_file('views', $name, $this->_extension);
        if(!$file)
        {
            $file = $this->phoenix->find_file('views', $name, 'html');
        }
		if ($file == false)
			throw new \Exception("View {$name} not found.");
			
		$this->path = $file;
	}

	/**
	 * Manages storing the data passed to the view as properties
	 *
	 * @param string $key Property name
	 * @param string $val Property value
	 * @return void
	 */
	public function __set($key, $val)
	{
		$this->_data[$key] = $val;
	}

	/**
	 * Manages checking whether a dynamic property has been defined or not
	 *
	 * @param string $key Property name
	 * @return boolean
	 */
	public function __isset($key)
	{
		return array_key_exists($key, $this->_data);
	}

	/**
	 * Manages accessing passed data as properties
	 *
	 * @param string   $key Property name
	 * @return mixed	Property value
	 * @throws \Exception If the property is not found
	 */
	public function __get($key)
	{
		if (array_key_exists($key, $this->_data))
			return $this->_data[$key];
		throw new \Exception("Value {$key} not set for view {$this->name}");
	}

    public function Insert($name)
    {
        $file = $this->phoenix->find_file('views', $name, $this->_extension);
        if(!$file)
        {
            $file = $this->phoenix->find_file('views', $name, 'html');
        }
        if ($file == false)
            throw new \Exception("View {$name} not found.");
        extract($this->helper->get_aliases());
        extract($this->_data);
        ob_start();
        include ($file);
        $template = ob_get_clean();
        ob_start();
        $template = strtr( $template, array('{' => '<?= ','}' => ' ?>'));
         eval(' ?>'.$template.'<?php ');
        $rendered = ob_get_clean();
        return $rendered;
    }
	/**
	 * Renders the template, all dynamically set properties
	 * will be available inside the view file as variables.
	 * Aliases form a View Helper will be added automatically.
	 * Example:
	 * <code>
	 * $view = $this->phoenix->view('frontpage');
	 * $view->title = "Page title";
	 * echo $view->render();
	 * </code>
	 *
	 * @return string Rendered template
	 * @see \PHPhoenix\View\Helper
	 */

	public function render()
	{
		extract($this->helper->get_aliases());
		extract($this->_data);
		ob_start();
        include($this->path);
        $template = ob_get_clean();//file_get_contents($this->path);
        ob_start();
        $template = strtr( $template, array('{' => "<?=",'}' => "?>"));
        eval(' ?>'.$template.'<?php ');
		//include($this->path);
        $result = ob_get_clean();
		return $result;
	}
	
}
