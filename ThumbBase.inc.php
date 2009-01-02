<?php

class ThumbBase 
{
	private $imported;
	private $imported_functions;
	
	public function __construct()
	{
		$this->imported				= array();
		$this->imported_functions	= array();
	}
	
	protected function imports($object)
	{
		$new_imports 		= new $object();
		$imports_name		= get_class($new_imports);
		$imports_function 	= get_class_methods($new_imports);
		
		array_push($this->imported, array($imports_name, $new_imports));
		
		foreach($imports_function as $key => $function_name)
		{
			$this->imported_functions[$function_name] = &$new_imports;
		}
	}
	
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