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
	private $importedFunctions;
	
	/**
	 * Class constructor
	 * 
	 * @return ThumbBase
	 */
	public function __construct ()
	{
		$this->imported				= array();
		$this->importedFunctions	= array();
	}
	
	/**
	 * Imports a plugin
	 * 
	 * This is where all the plugins magic happens!  This function "loads" the plugin functions, making them available as 
	 * methods on the class.
	 * 
	 * @param string $object The name of the object to import / "load"
	 */
	protected function imports ($object)
	{
		// the new object to import
		$newImport 			= new $object();
		// the name of the new object (class name)
		$importName			= get_class($newImport);
		// the new functions to import
		$importFunctions 	= get_class_methods($newImport);
		
		// add the object to the registry
		array_push($this->imported, array($importName, $newImport));
		
		// add teh methods to the registry
		foreach ($importFunctions as $key => $functionName)
		{
			$this->importedFunctions[$functionName] = &$newImport;
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
	public function __call ($method, $args)
	{
		if( array_key_exists($method, $this->importedFunctions))
		{
			$args[] = $this;
			return call_user_func_array(array($this->importedFunctions[$method], $method), $args);
		}
		
		throw new Exception ('Call to undefined method/class function: ' . $method);
	}

    /**
     * Returns $imported.
     * @see ThumbBase::$imported
     * @return array
     */
    public function getImported ()
    {
        return $this->imported;
    }
    
    /**
     * Returns $importedFunctions.
     * @see ThumbBase::$importedFunctions
     * @return array
     */
    public function getImportedFunctions ()
    {
        return $this->importedFunctions;
    }

}

?>