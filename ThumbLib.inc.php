<?php
/**
 * PhpThumb Library Definition File
 * 
 * This file contains the definitions for the PhpThumbFactory and the PhpThumb classes.
 * It also includes the other required base class files.
 * 
 * If you've got some auto-loading magic going on elsewhere in your code, feel free to
 * remove the include_once statements at the beginning of this file... just make sure that
 * these files get included one way or another in your code.
 * 
 * @author Ian Selby <ian@gen-x-design.com>
 * @copyright Copyright 2008 Gen X Design
 * @version 3.0
 * @package PhpThumb
 * @filesource
 */

include_once('ThumbBase.inc.php');

/**
 * PhpThumbFactory Object
 * 
 * This class is responsible for making sure everything is set up and initialized properly,
 * and returning the appropriate thumbnail class instance.  It is the only recommended way 
 * of using this library, and if you try and circumvent it, the sky will fall on your head :)
 * 
 * Basic use is easy enough.  First, make sure all the settings meet your needs and environment...
 * these are the static variables defined at the beginning of the class.
 * 
 * Once that's all set, usage is pretty easy.  You can simply do something like:
 * <code>$thumb = PhpThumbFactory::create('/path/to/file.png');</code>
 * 
 * Refer to the documentation for the create function for more information
 * 
 * @package PhpThumb
 * @subpackage Core
 */
class PhpThumbFactory
{
	/**
	 * Which implemenation of the class should be used by default
	 * 
	 * Currently, valid options are:
	 *  - imagick
	 *  - gd
	 *  
	 * These are defined in the implementation map variable, inside the create function
	 * 
	 * @var string
	 */
	public static $default_implemenation = 'imagick';
	/**
	 * Where the plugins can be loaded from
	 * 
	 * Note, it's important that this path is properly defined.  It is very likely that you'll 
	 * have to change this, as the assumption here is based on a relative path.
	 * 
	 * @var string
	 */
	public static $plugin_path = 'thumb_plugins/';
	
	public static function create($filename = '')
	{
		$implementation_map = array
		(
			'imagick' => 'ImagickThumb',
			'gd' => 'GdThumb'
		);
		
		$pt = PhpThumb::getInstance();
		$pt->loadPlugins(self::$plugin_path);
		
		if($pt->isValidImplementation(self::$default_implemenation))
		{
			$imp = $implementation_map[self::$default_implemenation];
			return new $imp($filename);
		}
		else if ($pt->isValidImplementation('gd'))
		{
			$imp = $implementation_map['gd'];
			return new $imp($filename);
		}
		else
		{
			throw new Exception('You must have either the GD or iMagick extension loaded to use this library');
		}
	}
}

class PhpThumb
{
	static $_instance;
	protected $_registry;
	protected $_implementations;
	
	/**
	 * 
	 * @return PhpThumb
	 */
	public static function getInstance()
	{
		if(!(self::$_instance instanceof self))
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	
	private function __construct()
	{
		$this->_registry		= array();
		$this->_implementations	= array('gd' => false, 'imagick' => false);
		
		$this->getImplementations();
	}
	
	private function getImplementations()
	{
		foreach($this->_implementations as $extension => $loaded)
		{
			if($loaded)
			{
				continue;
			}
			
			echo 'Extension: ' . $extension . "\n";
			
			if(extension_loaded($extension))
			{
				$this->_implementations[$extension] = true;
			}
		}
	}
	
	public function isValidImplementation($implementation)
	{
		if(array_key_exists($implementation, $this->_implementations))
		{
			return $this->_implementations[$implementation];
		}
		
		return false;
	}
	
	public function registerPlugin($plugin_name, $implementation)
	{
		if(!array_key_exists($plugin_name, $this->_registry) && $this->isValidImplementation($implementation))
		{
			$this->_registry[$plugin_name] = array('loaded' => false, 'implemenation' => $implementation);
		}
	}
	
	public function loadPlugins($plugin_path)
	{
		// strip the trailing slash if present
		if(substr($plugin_path, strlen($plugin_path) - 1, 1) == '/')
		{
			$plugin_path = substr($plugin_path, 0, strlen($plugin_path) - 1);
		}
		
		if($handle = opendir($plugin_path))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file == '.' || $file == '..')
				{
					continue;
				}
				
				include_once($plugin_path . '/' . $file);
			}
		}
	}
}



?>