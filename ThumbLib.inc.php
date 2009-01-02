<?php
include_once('ThumbBase.inc.php');

class PhpThumbFactory
{
	public static $default_implemenation = 'imagick';
	public static $plugin_path = 'thumb_plugins/';
	
	public static function create($filename = '')
	{
		$pt = PhpThumb::getInstance();
		$pt->loadPlugins(self::$plugin_path);
		
		if($pt->isValidImplementation(self::$default_implemenation))
		{
			// return new ImagickThumb
		}
		else if ($pt->isValidImplementation('gd'))
		{
			// return new GdThumb
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