<?php
/**
 * PhpThumb Base Class Definition File
 * 
 * This file contains the definition for the ThumbBase object
 * 
 * @author Ian Selby <ian@gen-x-design.com>
 * @copyright Copyright 2008 Gen X Design
 * @version 3.0
 * @package PhpThumb
 * @filesource
 */

/**
 * ThumbBase Class Definition
 * 
 * This is the base class that all implementations must extend.  It contains the 
 * core variables and functionality common to all implementations, as well as the functions that 
 * allow plugins to augment those classes.
 * 
 * @package PhpThumb
 * @subpackage Core
 */
class ThumbBase 
{
	/**
	 * All imported objects
	 * 
	 * An array of imported plugin objects
	 * 
	 * @var array
	 */
	private $imported;
	/**
	 * All imported object functions
	 * 
	 * An array of all methods added to this class by imported plugin objects
	 * 
	 * @var array
	 */
	private $imported_functions;
	
	/**
	 * Class constructor
	 * 
	 * @return ThumbBase
	 */
	public function __construct()
	{
		$this->imported				= array();
		$this->imported_functions	= array();
	}
	
	/**
	 * Imports a plugin
	 * 
	 * This is where all the plugins magic happens!  This function "loads" the plugin functions, making them available as 
	 * methods on the class.
	 * 
	 * @param string $object The name of the object to import / "load"
	 */
	protected function imports($object)
	{
		// the new object to import
		$new_import 		= new $object();
		// the name of the new object (class name)
		$import_name		= get_class($new_import);
		// the new functions to import
		$import_functions 	= get_class_methods($new_import);
		
		// add the object to the registry
		array_push($this->imported, array($import_name, $new_import));
		
		// add teh methods to the registry
		foreach($import_functions as $key => $function_name)
		{
			$this->imported_functions[$function_name] = &$new_import;
		}
	}
	
	/**
	 * Calls plugin / imported functions
	 * 
	 * This is also where a fair amount of plugins magaic happens.  This magic method is called whenever an "undefined" class 
	 * method is called in code, and we use that to call an imported function. 
	 * 
	 * You should NEVER EVER EVER invoke this function manually.  The universe will implode if you do... seriously ;)
	 * 
	 * @param string $method
	 * @param array $args
	 */
	public function __call($method, $args)
	{
		if(array_key_exists($method, $this->imported_functions))
		{
			return call_user_func_array(array($this->imported_functions[$method], $method), $args);
		}
		
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}
}

?>